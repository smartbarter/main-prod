<?php use Barter\Date;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
* Модель работает в личном кабинете компании
*/

class Company_model extends CI_Model
{
    //Функция подсчета суммы заказанных сделок
    public function sum_all_orders($buyer_deal_id)
    {
        $query = $this->db->select_sum('summa_sdelki')
            ->where('buyer_deal_id', $buyer_deal_id)
            ->where('status_deal', 1)
            ->get('deals');

        return $query->row()->summa_sdelki;
    }

    //Функция подсчета суммы заказанных сделок c учетом комиссии и купонов
    public function reserved_for_deals($buyer_deal_id)
    {
        $percent = 1 + PERCENT_SYSTEM / 100;
        $query = $this->db
            ->select("sum(
            case when (d.summa_sdelki * $percent - case when isnull(c.summa) then 0 else c.summa end) < 0 
            then 0 
            else (d.summa_sdelki * $percent - case when isnull(c.summa) then 0 else c.summa end) 
            end) as summa_sdelki")
            ->where('d.buyer_deal_id', $buyer_deal_id)
            ->join('coupons c', 'c.deal_id=d.deal_id', 'left')
            ->where('d.status_deal', 1)
            ->get('deals d');

        return $query->row()->summa_sdelki ?? 0;
    }

    //Создание заявки на кредит
    public function store_credit(int $company_id, int $summ)
    {
        $data = [
            'user_id' => $company_id,
            'summa' => $summ,
        ];

        if ($this->db->insert('credit', $data)) {
            return true;
        }

        return false;
    }

    //Данные компании по телефону
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

    //Данные компании по ИД
    public function find_company_data_by_deals_id($company_deal_id)
    {
        $this->db->where('for_deals_id', $company_deal_id);
        $query = $this->db->get('companies');

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    //Рекламирвование компании
    public function store(int $company_id, $from)
    {
        $this->db->trans_start();
        $res = $this->db->select('barter_balance')->from('companies')->where('company_id', $company_id)->get();

        if ($res) {
            $data = $res->row_array();
            if ($data['barter_balance'] < COST_SERVICE * 100) {
                $this->db->trans_complete();
                return false;
            }

            $this->db->where('company_id', $company_id)->update('companies', [
                'barter_balance' => $data['barter_balance'] - COST_SERVICE * 100,
            ]);
            $this->db->insert('deals', [
                'seller_deal_id' => '5ecd1b9fbc3d497',
                'buyer_deal_id' => $_SESSION['ses_company_data']['deals_id'],
                'summa_sdelki' => COST_SERVICE * 100,
                'status_deal' => 2,
                'comment_deal' => 'Заявка на рекламу',
                'date' => (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s'),
            ]);
            $this->db->insert('advert', [
                'company_id' => $company_id,
                'from' => $from,
                'date' => Date::now(),
            ]);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function find_vk_chat_id($company_deal_id)
    {
        $this->db->select('vk_chat_id');
        $this->db->where('user_deals_id', $company_deal_id);
        $query = $this->db->get('vk_notify');
        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    public function find_company_and_manager($company_deal_id)
    {
        $this->db->select('*, CASE WHEN sub_end > NOW() THEN 1 ELSE 0 END AS sub_status');
        $this->db->from('companies c');
        $this->db->where('c.for_deals_id', $company_deal_id);
        $query = $this->db->get();

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return $this->find_company_data_by_deals_id($company_deal_id);
    }

    public function find_company_detail_by_id_ext($company_id)
    {
        if ($company_id <= 0) {
            return false;
        }
        $this->db->select('*, CASE WHEN sub_end > NOW() THEN 1 ELSE 0 END AS sub_status');
        $this->db->from('companies c');
        $this->db->where('c.company_id', $company_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0)
        {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        return false;
    }

    public function find_company_detail_by_id($company_id)
    {
        if (! $company_id) {
            return false;
        }
        $this->db->select('
            company_id,
            company_name,
            description_company,
            logo,
            month_limit,
            barter_balance,
            credit_balance,
            registr_date,
            company_phone,
            for_deals_id,
            sverh_limit,
            adress,
            contact_name,
            social_inst,
            social_vk,
            company_site,
            geo_code,
            CASE WHEN sub_end > NOW() THEN 1 ELSE 0 END AS sub_status,
            DATE_FORMAT(was_online, "%d.%m.%Y %H:%i") as was_online,
            CASE WHEN was_online < NOW() - interval 30 minute THEN 0 ELSE 1 END AS online_status
        ');
        $this->db->select('(select COUNT(*) from deals where seller_deal_id = c.for_deals_id and status_deal = 2) as num_deals');
        $this->db->where('company_id', $company_id);
        $query = $this->db->get('companies c');
        if ($query && $query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        return false;
    }

    public function change_work_sverh_limit($company_id, $type_work)
    {
        $data = [
            'sverh_limit' => $type_work,
        ];

        $this->db->where('company_id', $company_id);
        $query = $this->db->update('companies', $data);

        if ($query) {
            return true;
        }

        return false;
    }

    //функция поиска компании по описанию ее деятельности
    //в выдаче участвую только активные компании, которые оплатили аккаунт
    public function search_company($find_data, string $city = null)
    {
        $to = (new DateTime('now',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s');
        $from = (new DateTime('now',
            new DateTimeZone('Europe/Moscow')))->sub(new DateInterval('P1M'))->format('Y-m-d H:i:s');
        $string = "(status = '2' OR status = '3')";
        $result = array();

        //Поиск по названию компании и номеру телефона
        $this->db->select('companies.company_id, company_name, city_name, logo, description_company, COUNT(cc.company_id) as count_cat')
            ->from('companies')
            ->where($string)
            ->where('hidden', false)
            ->join('company_categories cc',
                'cc.company_id = companies.company_id');

        $this->db->select("(select COUNT(*) from deals where seller_deal_id = companies.for_deals_id and status_deal = 2 and date between '$from' and '$to') as num_deals");

        if (! empty($city)) {
            $this->db->where('city_code', $city);
        }


        $this->db->group_start();
        $this->db->like('company_name', $find_data);
        $this->db->or_like('company_phone', $find_data);
        $this->db->group_end();

        $this->db->order_by('num_deals', 'desc');

        $this->db->group_by('companies.company_id')
            ->having('count_cat > 0');

        $query = $this->db->get();

        //Добавление к общему массиву
        if ($query->num_rows() > 0) {
            $result = array_merge($result, $query->result_array());
        }

        //Поиск по описанию
        $this->db->select('companies.company_id, company_name, city_name, logo, description_company, COUNT(cc.company_id) as count_cat')
            ->from('companies')
            ->where($string)
            ->where('hidden', false)
            ->join('company_categories cc',
                'cc.company_id = companies.company_id');

        $this->db->select("(select COUNT(*) from deals where seller_deal_id = companies.for_deals_id and status_deal = 2 and date between '$from' and '$to') as num_deals");

        if (! empty($city)) {
            $this->db->where('city_code', $city);
        }

        $this->db->like('description_company', $find_data);

        $this->db->order_by('num_deals', 'desc');

        $this->db->group_by('companies.company_id')
            ->having('count_cat > 0');

        $query = $this->db->get();

        //Добавление к общему массиву
        if ($query->num_rows() > 0) {
            $result = array_merge($result, $query->result_array());
        }

        if (!empty($result)) {
            return $this->unique_multidim_array($result,'company_id');
        }
        else {
            return false;
        }
    }


    //функция поиска заявок с определенным статусом
    //0 - отменена
    //1 - ожидает подтверждения от продавца
    //2 - совершена
    public function find_deals($seller_id, $status_deal)
    {
        $this->db->select('deal_id, buyer_deal_id, summa_sdelki, date, company_id, company_name, company_phone');
        $this->db->from('deals a');
        $this->db->join('companies b', 'b.for_deals_id = a.buyer_deal_id',
            'left');
        $this->db->where('a.status_deal', $status_deal);
        $this->db->where('a.seller_deal_id', $seller_id);
        $this->db->order_by('a.date', 'ASC');
        $query = $this->db->get();

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    //функция поиска рекомендуемых компаний
    public function recommended_company(string $city = null)
    {


        $this->db->select("company_id, 
        company_name, 
        description_company, 
        d.date, 
        logo, 
        geo_code, 
        sverh_limit, 
        month_limit, 
        barter_job, 
        hidden,
                           (select avg(star) from rate where to_id = c.company_id) as rate,
                          (select count(*) from deals where seller_deal_id = c.for_deals_id) as num_deals,
                          (select count(*) from fave where fave_id = c.company_id) as fave_count
                           ")
            ->from('companies c')
            ->join('deals d', 'd.seller_deal_id = c.for_deals_id', 'left')
            ->group_start()
            ->where('status', 2)
            ->or_where('status', 3)
            ->group_end()
            ->where('hidden', 0)
            ->where('d.status_deal', 2)
            ->order_by('d.deal_id', 'desc')
            ->limit(50);

        if (! empty($city)) {
            $this->db->where('city_code', $city);
        }
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            $array = $this->unique_multidim_array($query->result_array(),
                'company_id');

            return array_slice($array, 0, 20);
        }

        return false;
    }

    //Улучшенная версия с учетом посещенных категорий
    public function recommended_company_ext($company_id)
    {
        $MAX_CATEGORIES = 5;
        $MAX_COMPANIES = 25;
        //Берем самые популярные категории компании
        $query = $this->db->select('category_id, total')
            ->from('company_category_statistics')
            ->where('company_id', $company_id)
            ->limit($MAX_CATEGORIES)
            ->order_by('total', 'desc')
            ->order_by('date', 'desc')
            ->get();

        if ($query->num_rows() == 0) {
            return $this->recommended_company();
        }

        $categories = $query->result_array();

        $cat_count = 0;
        foreach ($categories as $cat) {
            $cat_count += $cat['total'];
        }

        $from = (new DateTime('first day of this month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d');
        $to = (new DateTime('last day of next month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d');

        $recommended_companies = [];
        foreach ($categories as $i => $cat) {
            $this->db->select("c.company_id, 
            company_name, 
            description_company, 
            logo, 
            geo_code, 
            sverh_limit, 
            month_limit, 
            barter_job, 
            hidden,
            adress,
            (select avg(star) from rate where to_id = c.company_id) as rate,
            (select count(*) from deals where seller_deal_id = c.for_deals_id) as num_deals,
            (month_limit - (select sum(summa_sdelki) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) and status_deal = 2 and date between '$from' and '$to')) as month_limit_rest,
            (select count(*) from fave where fave_id = c.company_id) as fave_count,
            cc.category_id
            ")
                ->from('companies c')
                ->join('company_categories cc', 'c.company_id = cc.company_id')
                ->where('status', 2)
                ->where('hidden', 0)
                ->where('cc.category_id', $cat['category_id'])
                ->limit(ceil($MAX_COMPANIES * $cat['total'] / $cat_count))
                ->order_by('month_limit_rest', 'desc')
                ->order_by('num_deals', 'desc');

            if (!empty($recommended_companies)) {
                $this->db->where_not_in('c.company_id', array_column($recommended_companies, 'company_id'));
            }

            $query = $this->db->get();

            $recommended_companies = array_merge($recommended_companies, $query->result_array());
        }

        if (!empty($recommended_companies)) {
            return $recommended_companies;
        }
        return false;
    }

    public function companies_categories($recommended_companies)
    {
        if (!isset($recommended_companies)) return [];
        $ids = '';
        foreach($recommended_companies as $company) {
            $ids .= $company.',';
        }
        $ids = substr($ids, 0, -1);

        $query = $this->db->select('c.company_id, cat.category_title')
            ->from('company_categories c')
            ->join('categories cat', 'c.category_id = cat.category_id')
            ->where("c.company_id in ($ids)")
        ->get();
        return $query->result_array();
    }

    private function unique_multidim_array($array, $key)
    {
        $temp_array = [];
        $i = 0;
        $key_array = [];

        foreach ($array as $val) {
            if (! in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }

        return $temp_array;
    }
    //функция поиска последних 2 зарегистрированных компаний
    //которые являются активными
    public function find_last_new_companies(string $city = null)
    {
        $this->db->select('company_id, company_name, description_company, logo, adress, company_phone');
        $this->db->select('(select count(*) from fave where fave_id = companies.company_id) as fave_count');
        $this->db->where('status', 2)
            ->where('hidden', false);
        if (! empty($city)) {
            $this->db->where('city_code', $city);
        }
        $this->db->limit(30);
        $this->db->order_by('company_id', 'DESC');
        $query = $this->db->get('companies');

        if ($query && $query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

        return false;
    }

    //функция для поиска максимального скидоса за сегодня
    //вытаскиваем данные компании, которая ее предоставляет
    public function find_max_sale_today(string $city = null)
    {
        $this->db->select(' * ');
        $this->db->from('discounts d');
        $this->db->join('companies c', 'd.company_id = c.company_id', 'left');
        $this->db->where('TIMESTAMPDIFF(DAY, end_date, NOW()) = 0', null, false);
        $this->db->where('c.status', 2)
            ->where('c.hidden', false);
        if (! empty($city)) {
            $this->db->where('for_search', $city);
        }
        $this->db->order_by('d.summa_skidki', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE
        return false;
    }

    //функция используется в профиле компании
    //тупо делать отдельный запрос, но времени уже нет адаптировать
    public function find_discount_company_today($company_id)
    {
        $this->db->select(' * ');
        $this->db->where('company_id', $company_id);
        $this->db->where('TIMESTAMPDIFF(DAY, end_date, NOW()) = 0', null, false);
        $this->db->limit(1);
        $query = $this->db->get('discounts');

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE
        return false;
    }

    //функция для поиска заказов и сделок
    //используем в controllers/company/order/index
    //и используем в controllers/company/order/deals
    public function find_my_orders_or_deals(
        $first_field_name,
        $two_field_name,
        $buyer_or_seller_deal_id,
        $num,
        $offset
    ) {
        $this->db->select('
                        c.company_id,
                        c.company_name,
                        c.logo,
                        c.city_name,
                        d.deal_id,
                        d.date,
                        d.summa_sdelki,
                        d.status_deal,
                        d.seller_deal_id,
                        d.buyer_deal_id,
                        d.comment_deal,
                        d.want_cancel
                        ');
        // $this->db->from('companies c');
        $this->db->join('deals d', 'd.' . $two_field_name . ' = c.for_deals_id');
        $this->db->where('d.' . $first_field_name, $buyer_or_seller_deal_id);
        $this->db->order_by('d.date', 'DESC');
        $query = $this->db->get('companies c', $num, $offset);

        if ($query && $query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем массив значений
            return $query->result_array();
        }

        return false;
    }

    public function count_all_my_orders_or_deals($field_name, $buyer_or_deal_id)
    {
        $this->db->where($field_name, $buyer_or_deal_id);
        $this->db->from('deals');

        return $this->db->count_all_results();
    }

    public function update_company_data($company_id, $data, $escape = true)
    {
        $this->db->where('company_id', $company_id);
        if ($escape) {
            $result = $this->db->update('companies', $data);
        }
        else {
            foreach ($data as $key => $val) {
                $this->db->set($key, $val, false);
            }
            $result = $this->db->update('companies');
        }

        if ($result) {
            return true;
        }

        return false;
    }

    public function find_news_detail_by_id($news_id)
    {
        if (! $news_id) {
            return false;
        }
        $this->db->select('*');
        $this->db->where('news_id', $news_id);
        $query = $this->db->get('news');
        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

//иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    public function isLiked(int $user_id, int $company_id)
    {
        $query = $this->db->select('id')
            ->from('fave')
            ->where([
                'user_id' => $user_id,
                'fave_id' => $company_id,
            ])
            ->get();
        if ($query->num_rows() > 0) {
            return 1;
        }
        return 0;
    }

    public function change_fave(int $user_id, int $company_id)
    {
        $query = $this->db->select('id')
            ->from('fave')
            ->where([
                'user_id' => $user_id,
                'fave_id' => $company_id,
            ])
            ->get();
        if ($query->num_rows() > 0) {
            $query = $this->db->delete('fave', [
                'user_id' => $user_id,
                'fave_id' => $company_id,
            ]);
            if ($query) return 0;
            else return -1;
        }
        else {
            $query = $this->db->insert('fave', [
                'user_id' => $user_id,
                'fave_id' => $company_id,
            ]);
            if ($query) return 1;
            else return -1;
        }
        return -1;
    }

    public function add_fave(int $user_id, int $company_id)
    {
        $query = $this->db->insert('fave', [
            'user_id' => $user_id,
            'fave_id' => $company_id,
        ]);

        if ($query) {
            return true;
        }

        return false;
    }

    public function delete_from_fave(int $user_id, int $company_id)
    {
        $query = $this->db->delete('fave', [
            'user_id' => $user_id,
            'fave_id' => $company_id,
        ]);

        if ($query) {
            return true;
        }

        return false;
    }

    public function get_faves(int $id)
    {
        $query = $this->db
            ->select('c.company_id, c.company_name, c.logo, c.description_company')
            ->from('fave f')
            ->where('f.user_id', $id)
            ->join('companies c', 'f.fave_id = c.company_id')
            ->get();

        if ($query && $query->num_rows() > 0) {
            return $query->result_array();
        }

        return false;
    }

    public function get_faves_company_for_me($user_id)
    {
        $query = $this->db->select('fave_id')
            ->from('fave')
            ->where('user_id', $user_id)
            ->get();

        if ($query->num_rows() > 0) {
            $array = [];
            foreach ($query->result_array() as $item) {
                $array[] = (int) $item['fave_id'];
            }

            return $array;
        }

        return false;
    }

    public function get_recommended_for_company_detail(int $id)
    {
        $cat_id = $this->db->select('category_id')
            ->from('company_categories')
            ->where('company_id', $id)
            ->limit(1)
            ->get();
        $cat_id = $cat_id->row_array()['category_id'];
        $companies
            =
            $this->db->select('c.company_id, c.logo, c.company_name, COUNT(distinct deals.deal_id) as num_deals, COUNT(distinct fave.id) as fave_count')
                ->from('companies c')
                ->join('company_categories cc', 'c.company_id = cc.company_id')
                ->where('cc.category_id', $cat_id)
                ->where('(c.status = 2 OR c.status = 3)')
                ->where('c.hidden', false)
                ->join('deals', 'deals.seller_deal_id = c.for_deals_id', 'left')
                ->join('fave', 'fave.fave_id = c.company_id', 'left')
                ->group_by('c.company_id')
                ->order_by('rand()')
                ->limit(8)
                ->get();

        return $companies->result_array();
    }

    public function getwithoutcategory()
    {
        $query = $this->db->query('select company_id 
                from companies where 
                    not exists(select *
        from company_categories
                        where company_id = companies.company_id) and (status = 2 or status = 3)');

        $array = [];
        foreach ($query->result_array() as $comp) {
            $array[] = $comp['company_id'];
        }

        return $array;
    }

    public function store_review(int $from, int $to, string $text)
    {

        $query = $this->db->insert('reviews', [
            'id_company_about_review' => $to,
            'author_review_id' => $from,
            'text_review' => $text,
        ]);

        if ($query) {

            return true;
        }

        return false;
    }

    public function change_data_company($data, $company_id)
    {
        $this->db->where('company_id', $company_id);
        $query = $this->db->update('companies', $data);

        if ($query) {
            return true;
        }

        return false;
    }

    public function store_review_comp(int $from, int $to, string $text)
    {
        return $this->db->insert('reviews_comp', [
            'to_id' => $to,
            'from_id' => $from,
            'text_rev' => $text,
        ]);
    }

    public function views(int $company)
    {
        $query = $this->db->select('total, today')
            ->where('company', $company)
            ->from('views')
            ->get();

        return ($query->num_rows() > 0)
            ? $query->row_array()
            : false;
    }

    public function update_view(int $company)
    {
        $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $cur_date = $date->format('Y-m-d');
        $update_date = $date->format('Y-m-d H:i:s');
        $db_date = $this->db->select("DATE_FORMAT(date, '%Y-%m-%d') as date")
            ->where('company', $company)
            ->from('views')
            ->get();
        $db_date = $db_date->row_array()['date'];
        $query = $this->db->select('today, total')
            ->where('company', $company)
            ->from('views')
            ->get();
        if ($query->num_rows() < 1) {
            $this->db->insert('views', [
                'company' => $company,
                'total' => 1,
                'today' => 1,
            ]);

            return;
        }
        $query = $query->row_array();
        if ($cur_date == $db_date) {
            $this->db->where('company', $company)
                ->update('views', [
                    'total' => ($query['total'] + 1),
                    'today' => ($query['today'] + 1),
                ]);
        } else {
            $this->db->where('company', $company)
                ->update('views', [
                    'today' => 1,
                    'total' => ($query['total'] + 1),
                    'date' => (string) $update_date,
                ]);
        }
    }
    
    public function update_category_statistics($company_id, $category_id) {

        $date = new DateTime('now', new DateTimeZone('Europe/Moscow'));

        $query = $this->db->select("date, today, total")
            ->where('company_id', $company_id)
            ->where('category_id', $category_id)
            ->from('company_category_statistics')
            ->get();

        //Если ни разу не заходил в данную категорию
        if ($query->num_rows() < 1) {
            $this->db->set('date', 'NOW()', false);
            $this->db->insert('company_category_statistics', [
                'company_id' => $company_id,
                'category_id' => $category_id,
                'total' => 1,
                'today' => 1,
            ]);
            return;
        }
        //Если заходил
        $last_seen_date = DateTime::createFromFormat('Y-m-d H:i:s', $query->row_array()['date']);

        $this->db->where('company_id', $company_id);
        $this->db->where('category_id', $category_id);
        $this->db->set('date', 'NOW()', false);

        if ($date->format('Y-m-d') == $last_seen_date->format('Y-m-d')) {
            $this->db->set('today', 'today + 1', false);
            $this->db->set('total', 'total + 1', false);
        } else {
            $this->db->set('today', 1, false);
            $this->db->set('total', 'total + 1', false);
        }
        return $this->db->update('company_category_statistics');
    }

    public function add_product($data, $image = null)
    {
        return $this->db->insert('products', [
            'company_id' => $data['company_id'],
            'title' => $data['product_title'],
            'description' => $data['product_description'],
            'price' => $data['product_price'],
            'image' => $image,
            'category' => $data['product_category'],
        ]);
    }

    public function get_count_company_products(int $company_id)
    {
        return $this->db->select('id')
            ->from('products')
            ->where('company_id', $company_id)
            ->count_all_results();
    }

    public function get_products(
        int $company_id,
        $offset = null,
        $ajax = true
    ) {
        $res = $this->db->select('*')
            ->from('products')
            ->where('company_id', $company_id)
            ->order_by('id', 'desc')
            ->get();
        if ($res->num_rows() > 0) {
            $products = $this->db->select('p.title, p.image, p.price, p.id, p.description, pcat.category_title')
                ->from('products p')
                ->join('product_categories pcat', 'pcat.category_id = p.category', 'left')
                ->where('company_id', $company_id)
                ->order_by('id', 'desc')
                ->get();

            if (!$ajax) {
                return [
                    'products' => $products->result_array(),
                    'count' => $res->num_rows(),
                ];
            }
            $array = [];
            foreach ($products->result_array() as $product) {
                if ($product['image'] == null) {
                    $product['image'] = 'default.svg';
                }
                $url = base_url();
                $el['html'] = <<<HTML
<div class="col-sm-6 col-md-4 col-lg-3">
    <div class="widget bg_light margin-b-30 padding-15">
                                    <p class="txt_center">
                                            <img src="{$url}uploads/products_image/{$product['image']}"  height="150px" style="box-shadow: 0px 0px 15px #00000047; max-width: 100%;">
                                    </p>
                                    <p style="height: 30px;overflow: hidden;"
                                       class="txt_center">{$product["title"]}</p>
                                    <div style="line-height: 40px;height: 40px;">
                                        <div style="float: left"><span style="font-size: 18px;color: #4285f4;">{$product['price']}<i class="fa fa-rub" aria-hidden="true"></i></span></div>
                                        </div>
                                </div>
</div>
HTML;
                $array[] = $el['html'];
            }

            return [
                'products' => $array,
                'per_page' => 8,
                'count' => $res->num_rows(),
            ];
        }

        return false;
    }

    public function get_latest_products()
    {
        return $this->db->select(' * ')
            ->from('products')
            ->order_by('id', 'desc')
            ->limit(12)
            ->get()
            ->result_array();
    }

    public function get_all_products(
        $countable = false,
        $category = 0,
        $num = null,
        $offset = null
    ) {
        $this->db->select('*, category_title')
            ->order_by('id', 'desc');

        $this->db->join('product_categories', 'category_id = category', 'left');

        if ($category > 0) $this->db->where('category', $category);

        if ($num !== null || $offset !== null) {
            $res = $this->db->limit($num, $offset)->get('products');
        } else {
            $res = $this->db->get('products');
        }
        if ($countable) {
            return $res->num_rows();
        }

        return $res->result_array();
    }

    public function delete_product(int $company, int $product_id)
    {
        $this->db->where('company_id', $company, false);
        $this->db->where('id', $product_id, false);
        $this->db->limit(1);
        return $this->db->delete('products');
    }

    public function search_product(
        string $search,
        $all = false,
        $limit = 8,
        $offset = null
    ) {
        $this->db->select('title, image, price, id, MATCH(title) AGAINST("'
            . $search . '" IN NATURAL LANGUAGE MODE) as relevance')
            ->from('products')
            ->where('MATCH(title) AGAINST("' . $search
                . '" IN NATURAL LANGUAGE MODE)')
            ->order_by('relevance', 'desc');
        if ($all) {
            return $this->db->get()->num_rows();
        } else {
            $res = $this->db->limit($limit, $offset)->get();
        }
        if ($res->num_rows() > 0) {
            return $res->result_array();
        }

        return false;
    }

    public function rate_star(int $from, int $to, float $rate)
    {
        return $this->db->insert('rate', [
            'from_id' => $from,
            'to_id' => $to,
            'star' => $rate,
        ]);
    }

    public function get_rate(int $company)
    {
        return $this->db->select('AVG(star) as rate')
            ->from('rate')
            ->where('to_id', $company)
            ->get()
            ->row_array();
    }

    public function get_new_dostypno_dlya_sdelok(
        int $company_id,
        int $new_limit
    ) {
        $res = $this->db->select('month_limit, dostupno_dlya_sdelok')
            ->from('companies')
            ->where('company_id', $company_id)
            ->get()
            ->row_array();
        $raznica = $new_limit - $res['month_limit'];

        return ($res['dostupno_dlya_sdelok'] + $raznica);
    }

    /**
     * @param  string  $deal_id
     *
     * @return array
     * @throws \Exception
     */
    public function getCashInfo(string $deal_id)
    {
        //        Кол-во сделок за месяц
        //        1 % от суммы этих сделок
        /*
         * status: new -- на рассмотрении
         *         denied -- отклонена
         *         confirmed -- принята
         * */
        $currentMonth = (new DateTime('now',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s');
        $created_at = $this->db->select('created_at')->from('cash')
            ->where([
                'company_deal_id' => $deal_id,
            ])
            ->order_by('id', 'desc')
            ->limit(1)
            ->get()
            ->row_array()['created_at'];

        $availableMonth = (new DateTime($created_at))->add(new DateInterval('P1M'))
            ->format('Y-m-d H:i:s');
        if ($availableMonth <= $currentMonth || $created_at === null) {
            $deals = $this->availableCash($deal_id);
            $cash = (double) $deals['cash'] * 0.0001;

            return [
                'cash' => $cash > MINIMUM_CASH
                    ? MINIMUM_CASH
                    : $cash,
                'access' => true,
            ];
        }

        return [
            'payback_at' => $created_at,
            'access' => false,
        ];
    }

    public function getCashback(string $deal_id, array $data)
    {
        $deals = $this->availableCash($deal_id);

        $cash = (double) $deals['cash'] * 0.0001;

        return $this->db->insert('cash', [
            'amount' => ($cash > MINIMUM_CASH)
                ? MINIMUM_CASH
                : $cash,
            'company_deal_id' => $deal_id,
            'card_num' => $data['card_num'],
            'bank_holder' => $data['bank_holder'],
        ]);
    }

    private function availableCash(string $deal_id)
    {
        $from = (new DateTime('first day of this month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s');
        $to = (new DateTime('last day of this month',
            new DateTimeZone('Europe/Moscow')))->format('Y-m-d H:i:s');

        return $this->db->select('SUM(summa_sdelki) as cash')
            ->where('seller_deal_id', $deal_id)
            ->where('status_deal', 2)
            ->where("date BETWEEN '$from' and '$to'")
            ->from('deals')
            ->get()
            ->row_array();
    }

    public function reserved(string $seller_id)
    {
        $query = $this->db->select_sum('summa_sdelki')
            ->where('seller_deal_id', $seller_id)
            ->where('status_deal', 1)
            ->limit(1)
            ->get('deals');

        return $query->row()->summa_sdelki;
    }

    public function likes(int $company_id)
    {
        return $this->db->where('fave_id', $company_id)
            ->from('fave')
            ->count_all_results();
    }

    public function get_all_companies($count = true, $limit = 8, $offset = 8, $city = null, $order_str = '')
    {
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();

        $this->db->group_start()
            ->where('status', 2)
            ->or_where('status', 3)
            ->group_end()
            ->where('hidden', false);

        if (! empty($city)) {
            $this->db->where('city_code', $city);
        }
        if ($count) {
            return $this->db->select(' * ')->from('companies')->count_all_results();
        }


        $this->db->select(" c.*,                        
         (select sum(summa_sdelki) from deals where seller_deal_id = c.for_deals_id and date between '$from' and '$to') as sum_deals,
            c.barter_job,");

        if(strlen($order_str) >= 2) {
            $sort_type = $order_str[0];
            if ($order_str[1] === 'a')
                $order = 'asc';
            else
                $order = 'desc';
        }
        else if (strlen($order_str) == 1){
            $sort_type = $order_str[0];
            $order = 'desc';
        }
        else {
            $sort_type = 0;
            $order = 'desc';
        }
        switch ($sort_type) {
            case 1:
                $this->db->order_by('registr_date', $order);
                break;
            case 2:
                $this->db->order_by('registr_date', $order);
                break;
            case 3:
                $this->db->order_by('registr_date', $order);
                break;
            case 4:
                $this->db->order_by('registr_date', $order);
                break;
            case 5:
                $this->db->order_by('registr_date', $order);
                break;
            default:
                $this->db->order_by('registr_date', 'desc');
        }


        return $this->db->get('companies c', $limit, $offset)->result_array();
    }
    public function get_all_news()
    {
            $query = $this->db
                ->select('n.date, n.title, n.img, n.description, n.news_id, n.status' )
                ->from('news n')
                ->order_by('n.date', 'DESC')
                ->where('n.status', 1)
                ->get();
            if ($query->num_rows() > 0) {
                return $query->result_array();
            }
            return false;

    }
    public function get_all_news_profile()
    {
        $query = $this->db
            ->select('n.date, n.title, n.img, n.description, n.news_id, n.status' )
            ->from('news n')
            ->order_by('n.date', 'DESC')
            ->where('n.status', 1)
            ->limit(3)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        return false;

    }

    public function get_prize_count_deal_companies()
    {
        $string = "(status = '2' OR status = '3')";
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();


        $this->db->select('company_id, company_name, description_company, logo, COUNT(distinct deal_id) as num_deals');
        $this->db->join('deals', 'deals.seller_deal_id = for_deals_id', 'left');
        $this->db->where($string)
            ->where('hidden', false);
        $this->db->limit(50);
        $this->db->where("date BETWEEN '$from' and '$to'");
        $this->db->group_by('company_id', 'DESC');
        $this->db->order_by('num_deals', 'desc');
        $query = $this->db->get('companies');

        if ($query && $query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

        return false;
    }

    public function get_prize_sum_deal_companies()
    {
        $string = "(status = '2' OR status = '3')";
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();


        $this->db->select('company_id, company_name, description_company, logo, SUM(summa_sdelki) as sum_deals');
        $this->db->join('deals', 'deals.seller_deal_id = for_deals_id', 'left');
        $this->db->where($string)
            ->where('hidden', false);
        $this->db->limit(50);
        $this->db->where("date BETWEEN '$from' and '$to'");
        $this->db->group_by('company_id', 'DESC');
        $this->db->order_by('sum_deals', 'desc');
        $query = $this->db->get('companies');

        if ($query && $query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->result_array();
        }

        return false;
    }

    public function getRefHref(int $company_id)
    {
        $this->load->helper('string');
        $res = $this->db->select('invite_link')
            ->where('company_id', $company_id)
            ->where('invite_link is not null')
            ->from('companies')
            ->limit(1)
            ->get();

        if ($res && $res->num_rows() > 0) {
            return $res->row_array()['invite_link'];
        }

        $string = random_string('alpha', 10);
        $this->db->where('company_id', $company_id)
            ->update('companies', [
                'invite_link' => $string,
            ]);

        return $string;
    }

    public function update_description_request(int $company_id, string $text)
    {
        return $this->db->insert('update_description', [
                'description' => $text,
                'company_id' => $company_id,
            ]
        );
    }

    public function can_change_description(int $company_id)
    {
        $res = $this->db->select('id')
            ->from('update_description')
            ->where('status', 0)
            ->where('company_id', $company_id)
            ->get();

        return ! ($res && $res->num_rows() > 0);
    }

    public function total_month_sales(string $deal_id)
    {
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();

        $res = $this->db->select('sum(summa_sdelki) as total')
            ->from('deals')
            ->where("date between '$from' and '$to'")
            ->where('seller_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();

        if ($res) {
            return $res->row_array();
        }

        return ['total' => 0];
    }
    public function total_month_buy(string $deal_id)
    {
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();

        $res = $this->db->select('sum(summa_sdelki) as total_buy')
            ->from('deals')
            ->where("date between '$from' and '$to'")
            ->where('buyer_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();

        if ($res) {
            return $res->row_array();
        }

        return ['total_buy' => 0];
    }
    public function total_month_count_buy(string $deal_id)
    {
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();
        $query = $this->db
            ->select('count(deal_id) as num')
            ->from('deals')
            ->where("date between '$from' and '$to'")
            ->where('buyer_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();
        $res = $query->row_array()['num'];
        return $res;


    }
    public function total_month_count_sell(string $deal_id)
    {
        $from = Date::firstDayOfThisMonth();
        $to = Date::now();
        $query = $this->db
            ->select('count(deal_id) as num')
            ->from('deals')
            ->where("date between '$from' and '$to'")
            ->where('seller_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();
        $res = $query->row_array()['num'];
        return $res;


    }
    public function total_all_month_sales(string $deal_id)
    {


        $res = $this->db->select('sum(summa_sdelki) as total')
            ->from('deals')
            ->where('seller_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();

        if ($res) {
            return $res->row_array();
        }

        return ['total' => 0];
    }
    public function total_all_month_buy(string $deal_id)
    {


        $res = $this->db->select('sum(summa_sdelki) as total_buy')
            ->from('deals')
            ->where('buyer_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();

        if ($res) {
            return $res->row_array();
        }

        return ['total_buy' => 0];
    }
    public function total_all_month_count_buy(string $deal_id)
    {

        $query = $this->db
            ->select('count(deal_id) as num')
            ->from('deals')
            ->where('buyer_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();
        $res = $query->row_array()['num'];
        return $res;


    }
    public function total_all_month_count_sell(string $deal_id)
    {

        $query = $this->db
            ->select('count(deal_id) as num')
            ->from('deals')

            ->where('seller_deal_id', $deal_id)
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get();
        $res = $query->row_array()['num'];
        return $res;


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

    public function get_deal_info($deal_id, $only_status = false)
    {
        $comp_deals_id = $this->db->select('for_deals_id')
            ->from('companies')
            ->where('company_id', $_SESSION['ses_company_data']['company_id'])
            ->get();
        $comp_deals_id = $comp_deals_id->row_array();

        if ($only_status) {
            $res = $this->db->select('status_deal, want_cancel')
                ->from('deals')
                ->where("deal_id", $deal_id)
                ->group_start()
                ->where("seller_deal_id", $comp_deals_id['for_deals_id'])
                ->or_where("buyer_deal_id", $comp_deals_id['for_deals_id'])
                ->group_end()
                ->get();
        }
        else {
            $res = $this->db->select('
        d.deal_id,
        summa_sdelki,
        c.summa as coupon_sum,
        date, 
        status_deal, 
        comment_deal,
        want_cancel,
        buyer_deal_id,
        (select company_id from companies where for_deals_id = seller_deal_id) as seller_id,
        (select company_id from companies where for_deals_id = buyer_deal_id) as buyer_id')
                ->from('deals d')
                ->join('coupons c', 'd.deal_id = c.deal_id', 'left')
                ->where("d.deal_id", $deal_id)
                ->group_start()
                ->where("seller_deal_id", $comp_deals_id['for_deals_id'])
                ->or_where("buyer_deal_id", $comp_deals_id['for_deals_id'])
                ->group_end()
                ->get();
        }

        if ($res) {
            return $res->row_array();
        }

        return false;
    }

    public function check_sub_status($company_id) {
//        $now = new DateTime('now', new DateTimeZone('Europe/Moscow'));
//        $sub_comp_end = DateTime::createFromFormat('Y-m-d H:i:s', $sub['sub_end']);
        $this->db->select('CASE WHEN sub_end > NOW() THEN 1 ELSE 0 END AS status');
        $this->db->from('companies');
        $this->db->where('company_id', $company_id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {//если нам вернулось како-то значение, то возвращаем строку значений
            $status = $query->row_array();
            return (int)$status['status'];
        }

        return -1;
    }

    public function get_company_coupons($company_id, $status = -1) {

        $this->db->select('
        coupon_id,
        summa, 
        deal_id,
        date_expire, 
        CASE WHEN status = 0 and date_expire < NOW() THEN 2 ELSE status END AS status');
        $this->db->from('coupons');
        $this->db->where('company_id', $company_id);
        $this->db->order_by('date_expire', 'desc');
        switch ($status) {
            case 0:
                $this->db->where('status', 0);
                $this->db->where('date_expire > NOW()');
                break;
            case 1:
                $this->db->where('status', 1);
                break;
            case 2:
                $this->db->where('status <>', 1);
                $this->db->where('date_expire < NOW()');
                break;
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }

        return [];
    }

    public function get_coupon($coupon_id, $company_id = 0) {

        $this->db->select('
        coupon_id,
        company_id,
        summa, 
        deal_id,
        date_expire, 
        CASE WHEN status = 0 and date_expire < NOW() THEN 2 ELSE status END AS status');
        $this->db->from('coupons');
        $this->db->where('coupon_id', $coupon_id);
        if ($company_id > 0) {
            $this->db->where('company_id', $company_id);
        }
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return [];
    }

    public function manual_statistics(string $deal_id, $date_start, $date_end) {

        $from = $date_start->format('Y-m-d');
        $to = $date_end->add(new DateInterval('P1D'))->format('Y-m-d');

        $deals_sell = $this->db
            ->select('count(deal_id) as deals, sum(summa_sdelki) as cash')
            ->from('deals')
            ->where('seller_deal_id', $deal_id)
            ->where("date between '$from' and '$to'")
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get()->row_array();
        $deals_buy = $this->db
            ->select('count(deal_id) as deals, sum(summa_sdelki) as cash')
            ->from('deals')
            ->where('buyer_deal_id', $deal_id)
            ->where("date between '$from' and '$to'")
            ->group_start()
            ->where('status_deal', 1)
            ->or_where('status_deal', 2)
            ->group_end()
            ->get()->row_array();

        return [
            'deals_sell' => $deals_sell['deals'],
            'deals_buy' => $deals_buy['deals'],
            'deals_all' => $deals_sell['deals'] + $deals_buy['deals'],
            'cash_sell' =>$deals_sell['cash'],
            'cash_buy' =>$deals_buy['cash'],
            'cash_all' =>$deals_sell['cash'] + $deals_buy['cash']
        ];
    }

    //Функция получания сессий
    public function getsessions()
    {
        $this->db->select('*');
        return $this->db->get('sessions')->result_array();
    }

    //Функция закрытия сессий по id
    public function closesessions($ses_ids)
    {
        if (empty($ses_ids)) return 0;

        $this->db->trans_start();
        $this->db->where_in('id', $ses_ids);
        $this->db->delete('sessions');
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            return 1;
        }
        else {
            return -1;
        }
    }
}
