<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cities_model extends CI_Model
{
    public function getAll()
    {
        $query = $this->db->select('city_kladr_id, city_name')->from('cities')->get();
        if ($query && $query->num_rows() > 0) {
            $cities = $query->result_array();

            foreach ($cities as $key => $val) {
                if ($val['city_name'] == 'Все города') {
                    unset($cities[$key]);
                    break;
                }
            }

            array_unshift($cities, [
                'city_kladr_id' => '0',
                'city_name'     => 'Все города',
            ]);

            return $cities;
        }

        return false;
    }

    public function getDefault(int $user_id)
    {
        $query = $this->db->select('for_search')
            ->where('company_id', $user_id)
            ->from('companies')
            ->limit(1)
            ->get();

        if ($query->num_rows() > 0) {
            return $query->row_array();
        }

        return false;
    }

    public function setCity(int $user_id, int $city_id)
    {
        return $this->db->where('company_id', $user_id)->update('companies', ['for_search' => $city_id]);
    }

    public function get_json_data(int $id)
    {
        $query = $this->db->select('c.company_id,
                    c.geo_code,
                    c.company_name,
                    c.adress')
            ->where('category_id', $id)
            ->from('company_categories cc')
            ->join('companies c', 'c.company_id = cc.company_id')
            ->where('c.adress IS NOT NULL', null, false)
            ->where('c.adress !=', '')
            ->group_start()
            ->where('c.status', 2)
            ->or_where('c.status', 3)
            ->group_end()
            ->where('c.hidden', 0)
            ->limit(600)
            ->get();
        if ($query->num_rows() > 0) {
            $array = [
                'type' => 'FeatureCollection',
            ];
            $res = $query->result_array();
            foreach ($res as $id => $comp) {

                if (!isset($comp['geo_code']) || $comp['geo_code'] == '') continue;
                $code = explode(' ', $comp['geo_code']);
                $code = array_reverse($code);
                if (!$this->agent->is_mobile()) {
                    $url =
                        "<a target='_blank' href='/company/cabinet/company_detail?company_id={$comp['company_id']}'>{$comp['company_name']} <i class=\"fa fa-external-link\" aria-hidden=\"true\"></i></a>";
                }
                else {
                    $url =
                        "<a target='_blank' onclick=\"document.getElementById('comp_data_manual').click(); open_company_detail({$comp['company_id']})\">{$comp['company_name']}</a>";
                }
                $array['features'][] = [
                    'type' => 'Feature',
                    'id' => $id,
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => $code,
                    ],
                    'properties' => [
                        'balloonContentHeader' => $comp['company_name'],
                        'balloonContentBody' => $url,
                        'balloonContentFooter' => $comp['adress'],
                    ],
                ];
            }

            return json_encode($array);
        }

        return false;
    }
}