<?php


namespace Barter;


class Taxi
{
    /**
     * @var int
     */
    private $company_id;

    public function __construct(int $company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * @return bool|array
     */
    public function hasTaxi()
    {
        $res = get_instance()->db->from('taxi')->select('status')->where('id_comp', $this->company_id)->get();
        if ($res && $res->num_rows() > 0) {
            return $res->row_array();
        }

        return false;
    }

    public function info(): array
    {
        $res = get_instance()->db->select('name_car, area')->from('taxi')->where('id_comp', $this->company_id)->get();
        if ($res) {
            return $res->row_array();
        }

        return [];
    }

    public static function update(int $company_id, array $data): bool
    {
        return get_instance()->db->where('id_comp', $company_id)->update('taxi', $data);
    }

    public static function availableUsers(): array
    {
        $res = get_instance()->db->select('c.company_id, t.name_car, t.area, c.company_name, c.contact_name, c.company_phone, c.logo, c.city_name')
            ->from('taxi t')
            ->join('companies c', 't.id_comp = c.company_id')
            ->where('t.status', 1)
            ->get();

        if ($res) {
            return $res->result_array();
        }

        return [];
    }

}
