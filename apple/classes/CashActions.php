<?php


namespace Barter;


class CashActions extends \CI_Model
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

    public function addBarter(int $amount): bool
    {
        $amount *= 100;
        $this->db->trans_start();
        $query = $this->db->select('barter_balance')
            ->from('companies')
            ->where('company_id', $this->company_id)
            ->get();
        if ($query) {
            $old_balance = $query->row_array();
            $this->db->where('company_id', $this->company_id)
                ->update('companies', [
                        'barter_balance' => (int) $old_balance['barter_balance'] + $amount,
                    ]
                );
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            if (! $status) {
                log_message('error',
                    sprintf('Не удалось обновить бартерный баланс пользователья с ID%d. Сумма изменения: %d',
                        $this->company_id, $amount));
            }
            return $status;
        }
        return false;
    }

    public function addCreditBalance(int $amount)
    {
        $amount *= 100;
        $this->db->trans_start();
        $query = $this->db->select('credit_balance')
            ->from('companies')
            ->where('company_id', $this->company_id)
            ->get();
        if ($query) {
            $old_balance = $query->row_array();
            $this->db->where('company_id', $this->company_id)
                ->update('companies', [
                        'credit_balance' => (int) $old_balance['credit_balance'] + $amount,
                    ]
                );
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            if (! $status) {
                log_message('error',
                    sprintf('Не удалось обновить кредитный баланс пользователья с ID%d. Сумма изменения: %d',
                        $this->company_id, $amount));
            }
            return $status;
        }
        return false;
    }

    /**
     * @param  int  $amount  Сумма в рублях
     *
     * @return bool
     */
    public function addRubBalance(int $amount)
    {
        $amount *= 100;
        $this->db->trans_start();
        $query = $this->db->select('rub_balance')
            ->from('companies')
            ->where('company_id', $this->company_id)
            ->get();
        if ($query) {
            $old_balance = $query->row_array();
            $this->db->where('company_id', $this->company_id)
                ->update('companies', [
                        'rub_balance' => (int) $old_balance['rub_balance'] + $amount,
                    ]
                );
            $this->db->trans_complete();
            $status = $this->db->trans_status();
            if (! $status) {
                log_message('error',
                    sprintf('Не удалось обновить рублевый баланс пользователья с ID%d. Сумма изменения: %d',
                        $this->company_id, $amount));
            }
            return $status;
        }
        return false;
    }
}
