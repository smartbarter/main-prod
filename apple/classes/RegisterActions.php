<?php


namespace Barter;


class RegisterActions extends \CI_Model
{
    public function isInFirstCity(int $company_id): bool
    {
        $res = $this->db->query("SELECT comp.rank as rank
FROM (
         SELECT company_id, @rn := @rn + 1 AS rank
         FROM
             companies, (
             SELECT @rn := 0) r
         WHERE
             city_code = (
             SELECT
             city_code
             FROM
             companies
             WHERE
             company_id = $company_id)) AS comp
WHERE comp.company_id = $company_id");
        return ! (($res && $res->num_rows() > 0) && $res->row_array()['rank'] > 300);
    }
}
