<?php


namespace Barter;


class Worker
{
    public static function getIdFromDealId(string $id)
    {
        $res = get_instance()->db->select('user_id')->from('workers')->where('for_deals_id', $id)->get();
        if ($res) {
            return $res->row_array()['user_id'];
        }

        return 1;
    }
}