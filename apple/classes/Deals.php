<?php


namespace Barter;

class Deals
{
    public static function createDeal(array $params): bool
    {
        return get_instance()->db->insert('deals', $params);
    }
}
