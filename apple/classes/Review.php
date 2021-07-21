<?php


namespace Barter;


class Review
{
    public static function getAll(int $company_id): array
    {
        $res = get_instance()->db->select('c.company_name, c.logo, rc.text_rev, rc.timestamp, c.company_id')
            ->from('reviews_comp rc')
            ->where('rc.to_id', $company_id)
            ->where('rc.text_rev <>', "")
            ->join('companies c', 'c.company_id = rc.from_id')
            ->order_by('rc.id', 'desc')
            ->get();

        if ($res) {
            return $res->result_array();
        }

        return [];
    }
}