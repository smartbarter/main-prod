<?php


namespace Barter;


class Companies
{
    public static function hidden(): array
    {
        $db = get_instance()->db;
        $res = $db->where('hidden', 1)->from('companies')->get();
        if ($res) {
            return $res->result_array();
        }
        return [];
    }
    public static function not_active(): array
    {
        $db = get_instance()->db;
        $res = $db->select('*, w.name as   worker_name')
            ->from('companies c')
            ->join('workers w', 'c.manager_id = w.for_deals_id', 'left')
            ->where('c.status ', 1)
            ->get();


        if ($res) {
            return $res->result_array();
        }
        return [];
    }

}

