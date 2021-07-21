<?php use Barter\Date;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
* Модель для управления категориями
*/

class Category_model extends CI_Model
{
    public function get_categories($city = null, int $parent = -1)
    {
        if($city == null)
        {
            $this->db->select("*, 
            (select count(category_id) from company_categories ccat
            left join companies comp 
            on comp.company_id = ccat.company_id 
            where ccat.category_id = c.category_id
            and (comp.status IN (2,3)) 
            and comp.hidden = 0) as count");
        }
        else
        {
            $this->db->select("*, 
            (select count(category_id) from company_categories ccat
            left join companies comp 
            on comp.company_id = ccat.company_id 
            where ccat.category_id = c.category_id
            and (comp.status IN (2,3)) 
            and comp.hidden = 0
            and comp.city_code = $city) as count");
        }

        $this->db->order_by('c.category_title','asc');

        if ($parent > -1) $this->db->where('c.parent', $parent);

        $query = $this->db->get('categories c');

        return $query->result_array();
    }

    public function get_product_categories()
    {
        $query = $this->db->get('product_categories');
        return $query->result_array();
    }

    public function find_company_categories($company_id)
    {
        if (!$company_id) {
            return false;
        }

        $this->db->select('cat.category_id, cat.category_title');
        $this->db->from('company_categories comp_cat');
        $this->db->join('companies company', 'comp_cat.company_id=company.company_id');
        $this->db->join('categories cat', 'comp_cat.category_id=cat.category_id');
        $this->db->where('company.company_id', $company_id);
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_active_companies_category(
        $cat_id,
        $num,
        $offset,
        string $city = null,
        int $sort_type = 0,
        $order = 'd'
    ) {

        $today = Date::now();
        $today_minus_30d = (new DateTime('now',
            new DateTimeZone('Europe/Moscow')))->sub(new DateInterval('P1M'))->format('Y-m-d');
        $first_day_month = Date::firstDayOfThisMonth();

        $limit_rest_query = "(c.month_limit - COALESCE((select sum(summa_sdelki) from deals d where d.seller_deal_id = c.for_deals_id and date between '$first_day_month' and '$today' limit 1), 0))";
        $this->db->select("
                        c.company_id,
                        c.company_name,
                        c.description_company,
                        c.registr_date,
                        c.logo,
                        c.city_code,
                        c.sverh_limit,
                        c.month_limit,
                        c.barter_job,
                        c.adress,
                        geo_code,
                        (select count(DISTINCT d.deal_id) as num_deals from deals d where d.seller_deal_id = c.for_deals_id limit 1) as num_deals,
                        (select count(DISTINCT d.deal_id) as num_deals from deals d where d.seller_deal_id = c.for_deals_id and date between '$today_minus_30d' and '$today' limit 1) as num_deals_30d,
                        $limit_rest_query as limit_rest,
                        (SELECT COUNT(DISTINCT fave.id) AS fave_count FROM fave WHERE fave.fave_id = cc.company_id LIMIT 1) AS fave_count,
                        ")
            ->join('companies c', 'c.company_id=cc.company_id', 'inner')
            ->where('cc.category_id', $cat_id)
            ->group_start()
            ->where('c.status',2)
            ->or_where('c.status',3)
            ->group_end()
            ->where('c.hidden', false);
        if (!(empty($city))) {
            $this->db->where('city_code', $city);
        }

        $this->db->group_by('c.company_id');

        if ($order == 'a') {
            $order = 'asc';
        }
        else {
            $order = 'desc';
        }

        switch ($sort_type) {
            case 0: //По кол-ву сделок за 30 дней
                $this->db->order_by('num_deals_30d', $order);
                break;
            case 1: //По общему количеству сделок
                $this->db->order_by('num_deals', $order);
                break;
            case 2: //По дате регистрации
                $this->db->order_by('c.registr_date', $order);
                break;
            case 3: //По лимиту
                $this->db->order_by('c.month_limit', $order);
                break;
            case 4: //По остатку лимита
                $this->db->where("$limit_rest_query > 0");
                $this->db->order_by('limit_rest', $order);
                break;
            case 5: //По лайкам
                $this->db->order_by('fave_count', $order);
                break;
            default: //По умолчанию - по общему количеству сделок
                $this->db->order_by('num_deals', 'desc');
                break;
        }

        $query = $this->db->get('company_categories cc', $num, $offset);

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем массив значений
            return $query->result_array();
        }

        return false;
    }

    public function count_all_companies_in_category($cat_id, string $city = null)
    {
        $string = "(c.status = '2' OR c.status = '3')";
        $this->db->join('companies c', 'c.company_id=comp_cat.company_id', 'left');
        $this->db->from('company_categories comp_cat');
        $this->db->where('category_id', $cat_id);
        $this->db->where($string);
        $this->db->where('c.hidden', false);
        if (!empty($city)) {
            $this->db->where('city_code', $city);
        }

        return $this->db->count_all_results();
    }

    //функция добавления новой категории компании
    //т.е. добавляем категорию в которую входит компания
    public function add_new_company_category($data)
    {
        if ($this->db->insert('company_categories', $data)) {
            return true;
        }

        return false;
    }

    //удаляем компанию из категории
    public function delete_company_from_category($company_id, $cat_id)
    {
        $this->db->where('category_id', $cat_id);
        $this->db->where('company_id', $company_id);
        if ($this->db->delete('company_categories')) {
            return true;
        }

        return false;
    }

    public function add_new_category($data)
    {
        if ($this->db->insert('categories', $data)) {
            return true;
        }

        return false;
    }

    public function delete_category($cat_id)
    {
        $this->db->where('category_id', $cat_id);
        if ($this->db->delete('categories')) {
            return true;
        }

        return false;
    }

    public function have_cat_parent($cat_id)
    {
        $this->db->where('parent', $cat_id);
        $query = $this->db->get('categories');

        return $query->num_rows() > 0;
    }

    public function have_companies_in_cat($cat_id)
    {
        $this->db->where('category_id', $cat_id);
        $query = $this->db->get('company_categories');

        return $query->num_rows() > 0;
    }

}
