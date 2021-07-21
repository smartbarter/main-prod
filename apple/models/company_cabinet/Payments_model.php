<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Payments_model extends CI_Model
{
    public function find_payments_company($company_id, $num, $offset)
    {

        $this->db->where('company_id', $company_id)
            ->where('status_payment', 1);

        $query = $this->db->get('payments_for_service', $num, $offset);
        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

//иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    public function count_all_payments_company($company_id)
    {
        $this->db->where('company_id', $company_id)
            ->where('status_payment', 1);
        $this->db->from('payments_for_service');

        return $this->db->count_all_results();
    }

    //немножко хуевая функция, и в модели лучше не иметь логики,
    //но куда денешься то? Время решает!
    /**
     * @return string
     * @throws Exception
     */
    public function cron_job_update_month_limit()
    {
        $minimum_sum_deals = 600; //Минимальная сумма сделок в рублях

        $this->db->select('c.company_id,
        c.rub_balance,  
        (select sum(summa_sdelki) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) 
        AND date between LAST_DAY(NOW() - INTERVAL 2 MONTH) + INTERVAL 1 DAY and LAST_DAY(NOW() - INTERVAL 1 MONTH) + INTERVAL 1 DAY) as sum_deals');
        $this->db->from('companies c');
        $this->db->where('c.status', 2);

        //Получаем таблицу и записываем в массив
        $query = $this->db->get();
        $companies = $query->result_array();

        if ($companies)
        {
            $this->db->trans_start();

            $companies_update_balance = []; //Обновления баланса
            $companies_update_status = []; //Смена статуса на 3

            foreach ($companies as $company)
            {
                if ( $company['sum_deals'] > $minimum_sum_deals * 100)
                {
                    $rub_balance = $company['rub_balance'] - COST_SERVICE * 100;

                    if($rub_balance < 0)
                    {
                        //Если денег на счете не хватает, добавляем в список для обновления статуса на 3
                        $companies_update_status[] = $company['company_id'];
                    }
                    else
                    {
                        //Если денег хватает - обновляем баланс
                        $companies_update_balance[] = [
                            'company_id' => $company['company_id'],
                            'rub_balance' => $rub_balance,
                        ];
                    }
                }//End if
            }//End foreach

            //Пишем данные о начисленной сумме для оплаты в этом месяце
            //Сумма оплаченных платежей
            $paid_count = count($companies_update_balance);
            $sum_paid_payments = $paid_count * (COST_SERVICE * 100);
            //Сумма всех начисленных платежей(в т.ч. и неоплаченных)
            $all_count = $paid_count + count($companies_update_status);
            $sum_all_payments = $all_count * (COST_SERVICE * 100);//переводим в копейки

            $data = [
                'summa_nachislena' => $sum_all_payments,
                'payment_summ' => $sum_paid_payments,
            ];
            $this->db->set('date', 'NOW()', false);
            $this->db->insert('payments_stat', $data);

            //Обновляем баланс компаний
            if (!empty($companies_update_balance))
            {
                $query = "INSERT INTO `companies` (`company_id`, `rub_balance`) VALUES ";

                foreach ($companies_update_balance as $comp)
                {
                    $query .= '('. (int) $comp['company_id'] . ',' . $comp['rub_balance'] . '),';
                }
                //Удаляем последнюю запятую
                $query = rtrim($query, ',');

                $query .= " ON DUPLICATE KEY UPDATE `rub_balance` = VALUES(`rub_balance`)";

                $this->db->query($query);
            }

            //Обновляем статус компаний
            $status = 3; //Какой статус ставить

            if (!empty($companies_update_status))
            {
                $query = "INSERT INTO `companies` (`company_id`, `status`) VALUES ";

                foreach ($companies_update_status as $comp)
                {
                    $query .= '('. $comp . ',' . $status . '),';
                }
                //Удаляем последнюю запятую
                $query = rtrim($query, ',');

                $query .= " ON DUPLICATE KEY UPDATE `status` = VALUES(`status`)";

                $this->db->query($query);
            }

            //Говорим, что транзакция окончена
            $this->db->trans_complete();

            if ($this->db->trans_status() === false) {
                return "fail";
            }

            return "success";
        }

        return "no_data";
    }

    public function yandex_payment_company_new($data, $sub_days)
    {
        $this->db->trans_start();

        //Берем инфу о компании из БД
        $sub = $this->db->select('sub_end')
            ->from('companies')
            ->where('company_id', $data['company_id'])
            ->limit(1)
            ->get()
            ->row_array();

        //Приводим к нормальному виду
        $now = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $sub_comp_end = DateTime::createFromFormat('Y-m-d H:i:s', $sub['sub_end']);

        //Добавление записи в таблицу платежей
        if ($sub_comp_end > $now) {
            //Если подписка активна
            $this->db->set('sub_add_start', "'".$sub['sub_end']."'", false);
            $this->db->set('sub_add_end', "'".$sub_comp_end->add(new DateInterval('P' . $sub_days . 'D'))->format('Y-m-d H:i:s')."'", false);
        }
        else {
            //Если подписка не активна
            $this->db->set('sub_add_start', 'NOW()', false);
            $this->db->set('sub_add_end', "NOW() + interval $sub_days day", false);
        }
        $this->db->set('date_payment', 'NOW()', false);
        $this->db->insert('payments_for_service', $data);

        //Обновление подписки в информации о компании
        if ($sub_comp_end > $now) {
            //Если подписка активна
            $this->db->set('sub_end', "sub_end + interval $sub_days day", false);
        }
        else {
            //Если подписка не активна
            $this->db->set('sub_start', 'NOW()', false);
            $this->db->set('sub_end', "NOW() + interval $sub_days day", false);
        }
        $this->db->where('company_id', $data['company_id']);
        $this->db->update('companies');

        //Сумма оплат за этот месяц
        $this->db->query('UPDATE `payments_stat` SET `payment_summ`=`payment_summ` + '
            . $data['summa']
            . ' WHERE MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');

        //говорим, что транзакция окончена
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                . '/apple/logs/transaction.log',
                date(DATE_RFC822) . ': Ошибка транзакции: ' . json_encode($data) . '('
                . $this->db->_error_message() . ')' . PHP_EOL, FILE_APPEND);

            return false;
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT']
            . '/apple/logs/transaction.log',
            date(DATE_RFC822) . ": Успешно: " . json_encode($data)
            . PHP_EOL, FILE_APPEND);

        return true;
    }

    public function sberbank_payment_company($data, $sub_days)
    {
        //Берем дату окончания подписки
        $sub = $this->db->select('sub_end')
            ->from('companies')
            ->where('company_id', $data['company_id'])
            ->limit(1)
            ->get()
            ->row_array();

        //Приводим к нормальному виду
        $now = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $sub_comp_end = DateTime::createFromFormat('Y-m-d H:i:s', $sub['sub_end']);

        $this->db->trans_start();

        //Добавление записи в таблицу платежей
        if ($sub_comp_end > $now) {
            //Если подписка активна
            $this->db->set('sub_add_start', "'" . $sub['sub_end'] . "'", false);
            $this->db->set('sub_add_end', "'" . $sub['sub_end'] . "' + interval $sub_days day", false);
        }
        else {
            //Если подписка не активна
            $this->db->set('sub_add_start', 'NOW()', false);
            $this->db->set('sub_add_end', "NOW() + interval $sub_days day", false);
        }
        $this->db->set('status_payment', 1, false);
        $this->db->where('payment_id', $data['payment_id']);
        $this->db->update('payments_for_service');

        //Обновление подписки в информации о компании
        if ($sub_comp_end > $now) {
            //Если подписка активна
            $this->db->set('sub_end', "sub_end + interval $sub_days day", false);
        }
        else {
            //Если подписка не активна
            $this->db->set('sub_start', 'NOW()', false);
            $this->db->set('sub_end', "NOW() + interval $sub_days day", false);
        }
        $this->db->where('company_id', $data['company_id']);
        $this->db->update('companies');

        //Сумма оплат за этот месяц
        $this->db->query('UPDATE `payments_stat` SET `payment_summ`=`payment_summ` + '
            . $data['summa']
            . ' WHERE MONTH(date) = MONTH(NOW()) AND YEAR(date) = YEAR(NOW())');

        //говорим, что транзакция окончена
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                . '/apple/logs/transaction.log',
                date(DATE_RFC822) . ': Ошибка транзакции: ' . json_encode($data) . '('
                . $this->db->_error_message() . ')' . PHP_EOL, FILE_APPEND);

            return false;
        }

        file_put_contents($_SERVER['DOCUMENT_ROOT']
            . '/apple/logs/transaction.log',
            date(DATE_RFC822) . ": Успешно: Компания ID " . $data['company_id'] .  '. Оплата ' . $data['summa'] / 100 . 'р.'
            . PHP_EOL, FILE_APPEND);

        return true;
    }

    //разовый запуск функции, который апдейтит всем компаниям баланс
    function update_rub_balance()
    {
        $this->db->trans_start();

        $this->db->select('company_id, rub_balance');
        $this->db->where('registr_date >', '2018-06-01 00:00:00');
        $this->db->where('registr_date <', '2018-06-10 23:59:59');
        $query_companies = $this->db->get('companies');
        $companies = $query_companies->result_array();

        $update_only_limit_query
            = "INSERT INTO `companies` (`company_id`, `rub_balance`) VALUES ";
        $params = [];
        foreach ($companies as $compan_deal_id) {
            $params[] = '(' . (int) $compan_deal_id['company_id'] . ','
                . (int) $compan_deal_id['rub_balance'] . ' + (12 * (' . COST_SERVICE
                * 100 . ')))';
        }
        $update_only_limit_query .= implode(',', $params)
            . " ON DUPLICATE KEY UPDATE `rub_balance` = VALUES(`rub_balance`)";

        $this->db->query($update_only_limit_query);

        //говорим, что транзакция окончена
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            // return FALSE;
            return "fail";
        } else {
            // return TRUE;
            return "success";
        }


    }

    public function change_payment_status($payment_id, integer $status) {
        return $this->db->set('status_payment', $status, true)
            ->where('payment_id', $payment_id)
            ->update('payments_for_service');
    }

    public function create_initial_payment($company_id) {
        $data = [
            'company_id' => $company_id,
            'type_payment' => 1,
            'summa' => 0,
            'status_payment' => 0,
        ];
        $this->db->set('date_payment', 'NOW()', false);
        $this->db->insert('payments_for_service', $data);
        //Получаем номер
        return $this->db->insert_id();
    }

    public function update_payment_data($payment_id, $data) {

        $this->db->where('payment_id', $payment_id);
        return $this->db->update('payments_for_service', $data);
    }

    public function get_payment_status($payment_id) {

        $query = $this->db->select('status_payment')
            ->from('payments_for_service')
            ->where('payment_id', $payment_id)
            ->limit(1)
            ->get();

        if($query->num_rows() > 0) {
            return $query->row_array()['status_payment'];
        }
        return -1;
    }

}
