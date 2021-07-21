<?php

class Referral_model extends CI_Model
{

    public function getReferrals(int $company_id): array
    {
        $temp =
            $this->db->select('c.company_id,
            max(pfs.payment_id) as pid,
            c.company_name,
            c.logo,
            c.registr_date,
            c.ref_paid,
            (select sum(summa_sdelki) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) and status_deal = 2) as deals_sum')
                ->from('companies c')
                ->join('payments_for_service pfs', 'pfs.company_id = c.company_id', 'left')
                ->where('c.id_who_invite_company', $company_id)
                ->order_by('company_id', 'desc')
                ->group_by('c.company_id')
                ->get();
        if ($temp && $temp->num_rows() > 0) {
            return $temp->result_array();
        }

        return [];
    }

    public function getReferrals_levels(int $company_id, int $levels = 0, int $curr_level = 0, int $parent = 0): array
    {
        $this->db->select("c.company_id,
            max(pfs.payment_id) as pid,
            c.company_name, 
            c.logo, 
            c.registr_date, 
            c.ref_paid,
            c.id_who_invite_company,
            $curr_level as level,
            $parent as parent,
            (select coalesce(sum(summa_sdelki), 0) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) and status_deal = 2 and date >= '2019-12-01') as deals_sum")
                ->from('companies c')
                ->join('payments_for_service pfs', 'pfs.company_id = c.company_id', 'left')
                ->where('c.id_who_invite_company', $company_id)
            ->group_by('c.company_id')
                ->order_by('c.company_id', 'desc');

        $temp = $this->db->get();

        if ($temp && $temp->num_rows() > 0) {
            $companies = $temp->result_array();
            if ($curr_level < $levels) {
                foreach ($companies as $comp) {
                    $comp_referrals = $this->getReferrals_levels($comp['company_id'], $levels,
                        $curr_level + 1,
                        $parent == 0 ? $comp['company_id'] : $parent);
                    if (!empty($comp_referrals)) {
                        $companies = array_merge($companies, $comp_referrals);
                    }
                }
            }
            return $companies;
        }

        return [];
    }

    /**
     * @param int $company_owner
     * @param int $company_referral
     * @param int $type 1 in barter balance, 2: AP
     *
     * @return bool
     */
    public function activeRefBalance(int $company_owner, int $company_referral, int $type = 1): array
    {
        $temp =
            $this->db->select('*, 
            (select sum(summa_sdelki) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) and status_deal = 2) as deals_sum')
                ->from('companies c')
                ->where('company_id', $company_referral)
                ->get();

        if ($temp && $temp->num_rows() > 0) {
            $current_status = $temp->row_array();
            if ((bool) $current_status['ref_paid']) {
                return [
                    'result' => false,
                    'message' => 'Ошибка! Бонус уже выплачен!',
                ];
            }

            if ((int)$current_status['deals_sum'] / 100 < 10000) {
                return [
                    'result' => false,
                    'message' => 'Ошибка! Сумма сделок компании меньше 10000 руб.! Бонус недоступен.',
                ];
            }

            $owner = $this->db->where('company_id', $company_owner)->from('companies')->get()->row_array();

            switch ($type) {
                //Пополнение бартерного баланса
                case 1:
                    $this->db->trans_start();

                    //Пополняем баланс владельца
                    $this->db->where('company_id', $company_owner)
                        ->update('companies', [
                            'barter_balance' => $owner['barter_balance'] + COST_SERVICE * 100,
                        ]);

                    //Пополняем баланс владельца
                    $this->db->where('company_id', $company_referral)
                        ->update('companies', [
                            'ref_paid' => 1,
                        ]);

                    //Добавляем совершенную сделку
                    $this->db->set('date', 'NOW()', false);
                    $this->db->insert('deals', [
                        'seller_deal_id' => $owner['for_deals_id'],
                        'buyer_deal_id' => ADMIN_DEAL_ID,
                        'summa_sdelki' => COST_SERVICE * 100,
                        'status_deal' => 2,
                        'comment_deal' => 'Пополнение за приглашенную компанию'
                    ]);

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === false) {
                        return [
                            'result' => false,
                            'message' => 'Ошибка начисления бартерного баланса!',
                        ];
                    }
                    return [
                        'result' => true,
                        'message' => 'Бонус в размере ' . COST_SERVICE .' успешно начислен на бартерный баланс!'
                    ];

                //Продление АП
                case 2:
                    $this->db->trans_start();

                    $sub_days = 28;
                    //Получаем информацию о окончании АП
                    $sub = $this->db->select('sub_end')
                        ->from('companies')
                        ->where('company_id', $company_owner)
                        ->limit(1)
                        ->get()
                        ->row_array();

                    //Приводим к нормальному виду
                    $now = new DateTime('now', new DateTimeZone('Europe/Moscow'));
                    $sub_comp_end = DateTime::createFromFormat('Y-m-d H:i:s', $sub['sub_end']);

                    //Добавление записи в таблицу платежей
                    if ($sub_comp_end > $now) {
                        //Если подписка активна
                        $this->db->set('sub_add_start', "'".$sub['sub_end']."'", false);
                        $this->db->set('sub_add_end', "'".$now->add(new DateInterval('P' . $sub_days . 'D'))->format('Y-m-d H:i:s')."'", false);
                    }
                    else {
                        //Если подписка не активна
                        $this->db->set('sub_add_start', 'NOW()', false);
                        $this->db->set('sub_add_end', "NOW() + interval $sub_days day", false);
                    }

                    $this->db->set('date_payment', 'NOW()', false);
                    $data = [
                        'company_id' => $company_owner,
                        'referral_id' => $company_referral, //ИД реферала, с которого получен бонус
                        'type_payment' => 1,
                        'summa' => COST_SERVICE * 100,
                    ];
                    $this->db->insert('payments_for_service', $data);

                    //Обновление подписки в информации о компании
                    if ($sub_comp_end > $now) {
                        //Если подписка активна
                        $this->db->set('sub_end', "sub_end + interval $sub_days day", false);
                    }
                    else {
                        //Если подписка не активна
                        $this->db->set('sub_start', 'NOW()', false);
                        $this->db->set('sub_end', "NOW() + interval $sub_days day", false);
                    }
                    $this->db->where('company_id', $data['company_id']);
                    $this->db->update('companies');

                    //Пишем, что бонус за компанию получен
                    $this->db->where('company_id', $company_referral)
                        ->update('companies', [
                            'ref_paid' => 1,
                        ]);

                    //говорим, что транзакция окончена
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === false) {
                        return [
                            'result' => false,
                            'message' => 'Ошибка перевода бонуса в счет АП!',
                        ];
                    }
                    return [
                        'result' => true,
                        'message' => 'Бонус успешно переведен в счет АП!'
                    ];
                case 3:
                    $referral_data = $this->db->select("case when registr_date between '2019-12-01 00:00:00' and '2020-01-01 00:00:00' then 1 else 0 end as reg_december")
                        ->where('company_id', $company_referral)
                        ->get('companies')
                        ->row_array();

                    if ((int)$referral_data['reg_december'] == 0) {
                        return [
                            'result' => false,
                            'message' => 'Ошибка! Компания зарегистрирована не в декабре 2019 года!'
                        ];
                    }

                    $this->db->trans_start();

                    //Пополняем баланс владельца
                    $this->db->where('company_id', $company_owner)
                        ->update('companies', [
                            'barter_balance' => $owner['barter_balance'] + COST_SERVICE * 600,
                        ]);

                    //Меняем статус реферала на 1
                    $this->db->where('company_id', $company_referral)
                        ->update('companies', [
                            'ref_paid' => 1,
                        ]);

                    //Добавляем совершенную сделку
                    $this->db->set('date', 'NOW()', false);
                    $this->db->insert('deals', [
                        'seller_deal_id' => $owner['for_deals_id'],
                        'buyer_deal_id' => ADMIN_DEAL_ID,
                        'summa_sdelki' => 3000 * 100,
                        'status_deal' => 2,
                        'comment_deal' => 'Пополнение за приглашенную компанию в Декабре 2019. 3000 БР вместо обычных 500 БР!'
                    ]);

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === false) {
                        return [
                            'result' => false,
                            'message' => 'Ошибка начисления бартерного баланса!',
                        ];
                    }
                    return [
                        'result' => true,
                        'message' => 'Бонус в размере 3000 БР успешно начислен на бартерный баланс!'
                    ];

            }//End switch
        }//End if temp
        return [
            'result' => false,
            'message' => 'Ошибка! Компания-реферал не найдена!'
        ];
    }

    //Не используется
    public function payout(int $company_owner, int $company_referral)
    {
        $temp =
            $this->db->select('(select sum(summa_sdelki) from deals where (seller_deal_id = c.for_deals_id or buyer_deal_id = c.for_deals_id) and status_deal = 2) as deals_sum')
                ->from('companies c')
                ->where('company_id', $company_referral)
                ->get();
        if ($temp && $temp->num_rows() > 0) {
            $current_status = $temp->row_array();
            if ((bool)$current_status['ref_paid']) {
                return [
                    'result' => false,
                    'message' => 'Ошибка! Бонус уже выплчен!',
                ];
            }
            if ((int)$current_status['deals_sum'] / 100 < 10000) {
                return [
                    'result' => false,
                    'message' => 'Ошибка! Сумма сделок компании меньше 10000 руб.!',
                ];
            }
        }
        else {
            return [
                'result' => false,
                'message' => 'Компания не найдена',
            ];
        }
        $this->db->trans_start();
        $data = array(
            'company_id' => $company_owner,
            'amount' => 50000,
            'status' => 1,
        );
        $this->db->insert('cash', $data);

        $this->db->set('ref_paid', 1)
            ->where('company_id', $company_referral)
            ->update('companies');

        $result = $this->db->trans_complete();

        return [
            'result' => $result,
            'message' => ($result ? 'Успешно' : 'Ошибка'),
        ];
    }

    public function getCompanyReferrals(int $company_id): array
    {
        $res = $this->db->select('company_name, company_id, ref_paid')
            ->from('companies')
            ->where('id_who_invite_company', $company_id)
            ->get();
        if ($res && $res->num_rows() > 0) {
            return $res->result_array();
        }

        return [];
    }

    public function getTotalWidthdrawal(int $company_id)
    {
        $res = $this->db->select('sum(summa) as ref_sum')
            ->from('referral_bonus_withdrawals')
            ->where('company_id', $company_id)
            ->get()
            ->row_array();

        return $res['ref_sum'];
    }

    public function createWidthdrawal(int $company_id, int $deal_id, int $sum) {

        $data = array(
            'company_id' => $company_id,
            'deal_id' => $deal_id,
            'summa' => $sum,
        );
        $this->db->insert('referral_bonus_withdrawals', $data);

        return $this->db->insert_id();
    }

}