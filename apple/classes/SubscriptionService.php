<?php


namespace Barter;


class SubscriptionService extends \CI_Model
{
    /**
     * @var int
     */
    private $company_id;

    public function __construct(int $company_id)
    {
        parent::__construct();
        $this->company_id = $company_id;
    }

    /**
     * Проверка на наличие абон платы
     *
     * @return bool
     */
    public function checkSubscriptionStatus(): bool
    {
        $res = $this->db->select('status')
            ->from('companies')
            ->where('company_id', $this->company_id)
            ->get();
        if ($res) {
            $data = $res->row_array();
            return ! ($data['status'] !== '2');
        }
        return false;
    }
}
