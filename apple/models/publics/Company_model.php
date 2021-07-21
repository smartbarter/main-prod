<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
* Модель работает на публичной стороне приложения
*/

class Company_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    //функция регистрации компании
    public function reg_company(
        $city_data,
        $company_data,
        $phone_who_invited_company = ''
    ) {
        //начинаем транзакцию
        $this->db->trans_start();

        //1 шаг - ищем город
        $result_find_city
            = $this->find_city_company($city_data['city_kladr_id']);

        if ($result_find_city) {
            //если вернулось TRUE, значит города в БД нет и мы его пишем
            $this->add_new_city($city_data);
        }

        //2 шаг - регистрируем компанию

        //ищем партнера, который пригласил эту компанию
        if ($phone_who_invited_company != '') {
            $result_find_phone
                = $this->find_company_by_phone($phone_who_invited_company);

            if ($result_find_phone) {//если нашли компанию
                //Получаем ИД представителя, если приглашающая компания принадлежит ему
                $delegate = $this->get_delegate_deal_id($result_find_phone['manager_id']);
                if ($delegate) {
                    //Если так, делаем представителя менеджером этой компании
                    $company_data += [
                        'manager_id' => $delegate,
                    ];
                }
                $company_data += [
                    'id_who_invite_company' => $result_find_phone['company_id'],
                ];
            }
        }
        $company_data += [
            'sub_end' => (new DateTime('now', new DateTimeZone('+3 UTC')))->add(new DateInterval('P28D'))->format('Y-m-d H:i:s'),
        ];

        //2 шаг - регистрируем компанию
        $company_id = $this->add_new_company($company_data);

        //говорим, что транзакция окончена
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            //если транзакция не удалась
            return false;
        }
//иначе, возвращаем данные, если транзакция прошла успешно
        return $company_id;
    }

    public function get_delegate_deal_id($manager_deal_id)
    {
        $query = $this->db->select('w2.for_deals_id')
            ->from('workers w1')
            ->join('workers w2', 'w1.delegated_by = w2.user_id', 'left')
            ->where('w1.for_deals_id', $manager_deal_id)
            ->get()
            ->row_array();
        if ($query['for_deals_id'] != null) {
            return $query['for_deals_id'];
        }
        return false;
    }

    public function count_success_deals()
    {
        $this->db->where('status_deal', 2);
        $this->db->from('deals');
        $result = $this->db->count_all_results();
        return $result;
    }
    public function count_active_companies()
    {
        $string = "(status = '2' OR status = '3')";
        $this->db->where($string);
        $this->db->from('companies');
        $result = $this->db->count_all_results();
        return $result;
    }
    public function get_previous_month_deals_money()
    {
        $from = (new DateTime('first day of previous month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d');
        $to = (new DateTime('last day of previous month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d');

        $query = $this->db->select('SUM(summa_sdelki) as cash')
            ->from('deals')
            ->where('date >=', $from)
            ->where('date <=', $to)
            ->where('status_deal', 2)
            ->get();

        return $query->row_array();
    }
    private function find_city_company($kladr_city_id)
    {

        $this->db->where('city_kladr_id', $kladr_city_id);
        $this->db->limit(1);
        $query = $this->db->get('cities');

        return !($query->num_rows() > 0);

    }

    public function find_who_invited_company($phone)
    {
        $this->db->where('company_phone', $phone);
        $this->db->limit(1);
        $query = $this->db->get('companies');

        return $query->num_rows() > 0;
    }

    public function add_new_city($city_data)
    {

        $result = $this->db->insert('cities', $city_data);

        if ($result) {
            return true;
        }

        return false;
    }

    public function add_new_company($data)
    {

        $this->db->set('registr_date', 'NOW()', false);
        $result = $this->db->insert('companies', $data);

        if ($result) {
            return $this->db->insert_id();
        }

        return false;

    }

    public function find_company_by_phone($phone)
    {

        $this->db->where('company_phone', $phone);
        $query = $this->db->get('companies');

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

//иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    //ищем манагера в городе компании
    public function find_managers_in_company_city($city_kladr_id)
    {

        $this->db->select('vk_chat_id');
        $this->db->from('vk_notify a');
        $this->db->join('workers b', 'b.for_deals_id=a.user_deals_id', 'left');
        $this->db->join('manager_cities c', 'c.manager_id=b.user_id', 'left');
        $this->db->where('b.active_bot', 1);
        $this->db->where('b.status', 2);
        $this->db->where('c.city_kladr_id', $city_kladr_id);

        $query = $this->db->get();
        if ($query->num_rows() != 0) {
            return $query->result_array();
        }

        return false;
    }

    public function find_last_three_company_for_home_page()
    {
        $this->db->select('company_id,
                            company_name,
                            description_company,
                            registr_date, logo');
        $this->db->where('status', 2);
        $this->db->limit(3);
        $this->db->order_by('registr_date', 'DESC');
        $query = $this->db->get('companies');

        if ($query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

//иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    public function get_phone_number_for_ref(string $id)
    {
        $query = $this->db->select('company_phone')
            ->from('companies')
            ->where('invite_link', $id)
            ->get();

        return ($query->num_rows() > 0) ? $query->row_array()['company_phone'] : false;
    }

    public function update_balance(int $company_id, float $amount)
    {
        file_put_contents($_SERVER['DOCUMENT_ROOT']
            .'/apple/logs/transaction.log',
            date(DATE_RFC822).": $company_id, $amount".PHP_EOL, FILE_APPEND);
        $this->db->trans_start();
        $data = [
            'company_id' => $company_id,
            'amount' => $amount,
        ];
        $this->db->insert('payments_online', $data);

        $balance = $this->db->select('barter_balance')
            ->where('company_id', $company_id)
            ->get('companies');
        $balance = $balance->row_array();
        $balance['barter_balance'] += $amount;

        $this->db->where('company_id', $company_id);
        $this->db->update('companies', [
            'barter_balance' => $balance['barter_balance'],
        ]);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                sprintf('%s: Ошибка транзакции: %s(%s)%s', date(DATE_RFC822), json_encode(array_merge($data, $balance)),
                    $this->db->_error_message(), PHP_EOL), FILE_APPEND);

            return false;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT']
            .'/apple/logs/transaction.log',
            date(DATE_RFC822).": Успешно: ".json_encode(array_merge($data,
                $balance)).PHP_EOL, FILE_APPEND);

        return true;
    }

    public function send_pass(string $phone, string $password)
    {
        $defaults = [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => 'https://gate.smsaero.ru/v2/sms/send/',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => http_build_query([
                'number' => $phone,
                'text' => 'https://barter-business.ru'.PHP_EOL.'Логин: '.$phone
                    .PHP_EOL.'Пароль: '.$password,
                'sign' => 'Alahona',
                'channel' => 'DIRECT',
            ]),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERPWD => 'nickolaev.dany@gmail.com'.":".SMSAERO,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;

//        return true;
    }

    public function send_sms_activation_code(string $phone, string $message)
    {
        $defaults = [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => 'https://gate.smsaero.ru/v2/sms/send/',
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_POSTFIELDS => http_build_query([
                'number' => $phone,
                'text' => 'Ваш код подтверждения: '.$message,
                'sign' => 'Alahona',
                'channel' => 'DIRECT',
            ]),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERPWD => 'nickolaev.dany@gmail.com'.":".SMSAERO,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        ];

        $ch = curl_init();
        curl_setopt_array($ch, $defaults);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    /**
     * @param  string  $company
     *
     * @throws \Exception
     */
    public function createDealForRegister(string $company)
    {
        $this->db->insert('deals', [
            'seller_deal_id' => $company,
            'buyer_deal_id' => ADMIN_DEAL_ID,
            'summa_sdelki' => COST_SERVICE * 100,
            'status_deal' => 1,
            'comment_deal' => 'За регистрацию',
            'date' => (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s'),
        ]);
    }

    public function createCouponForRegister(int $company_id)
    {
        //Получаем настройки
        $query = $this->db->get('settings');
        if ($query->num_rows() < 1) {
            return false;
        }
        $settings = array();
        foreach ($query->result_array() as $set) {
            $settings[$set['name']] = $set['value'];
        }

        //Создаем купон
        $this->db->set('date_expire', 'NOW() + interval ' . $settings['coupon_register_expire'] . ' day', false);
        $this->db->insert('coupons', [
            'company_id' => $company_id,
            'summa' => $settings['coupon_register_sum'],
            'status' => 0,
            'deal_id' => 0
        ]);
    }

    public function createDealFromAdmin($company_id, $sum, string $message, $status_deal = 1)
    {
        $query = $this->db->select('for_deals_id')
            ->from('companies')
            ->where('company_id', $company_id)
            ->get();

        if($query->num_rows() > 0) {

            $comp_data = $query->row_array();
            $this->db->insert('deals', [
                'seller_deal_id' => $comp_data['for_deals_id'],
                'buyer_deal_id' => ADMIN_DEAL_ID,
                'summa_sdelki' => $sum, //Сумма в копейках!
                'status_deal' => $status_deal,
                'comment_deal' => $message,
                'date' => (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s'),
            ]);

            return $this->db->insert_id();
        }
        else {
            log_message('error', 'Unable to find company with id' . $company_id . '. Message: ' . $message);
            return -1;
        }
    }

    public function update_company_geocode($company_id)
    {
        $query = $this->db->select('company_id, adress, city_name')
            ->from('companies')
            ->where('adress !=', '')
            ->where('adress is not null', null, false)
            ->where('company_id', $company_id)
            ->get();

        $company = $query->row_array();

        $result = false;
        $addr = '';

        if (!stripos($company['adress'], $company['city_name'])) {
            $addr = 'г+' . $company['city_name'] . ',';
        }
        $addr .= str_replace(' ', '+', $company['adress']);

        $url = "https://geocode-maps.yandex.ru/1.x/?";
        $options = array(
            "format"=>'json',
            "apikey"=>YANDEX_APIKEY,
            "geocode"=>$addr,
            "results"=>1);

        $url .= http_build_query($options,'','&');

        $res = json_decode(file_get_contents( $url ));
        $pos = $res->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos;

        if ($pos != null) {
            $this->db->where('company_id', $company['company_id'])
                ->update('companies', ['geo_code' => $pos]);
            $result = true;
        }

        return $result;
    }

    public function find_company_and_manager($company_deal_id)
    {
        $this->db->select('*, CASE WHEN sub_end > NOW() THEN 1 ELSE 0 END AS sub_status');
        $this->db->from('companies c');
        $this->db->where('c.for_deals_id', $company_deal_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return [];
    }

}