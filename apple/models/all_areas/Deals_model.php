<?php if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
* Модель для работы со сделками
*/

class Deals_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        if (! defined('AGENT_CASH_FROM_COMPANY_PAYMENTS')) {
            define('AGENT_CASH_FROM_COMPANY_PAYMENTS', 0.1);
        }
    }

    public function get_deal_info($deal_id)
    {
        $this->db->select('summa');
        $this->db->where('deal_id', $deal_id);
        $query = $this->db->get('deals');

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row_array();
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    //функция для проверки статуса сделки
    public function get_status_deal($buyer_deal_id, $deal_id)
    {

        $this->db->select('status_deal');
        $this->db->where('buyer_deal_id', $buyer_deal_id);
        $this->db->where('deal_id', $deal_id);
        $query = $this->db->get('deals');

        if ($query->num_rows()
            > 0
        ) {//если нам вернулось како-то значение, то возвращаем строку значений
            return $query->row()->status_deal;
        }

        //иначе возвращаем FALSE - т.е. компании с таким номером нет
        return false;
    }

    //фнкция создания сделки
    public function create_new_deal($data, $coupon_id = 0)
    {
        // TODO изменить лимит у продавца
//        $seller_data = $this->db->select('dostupno_dlya_sdelok')
//            ->from('companies')
//            ->where('for_deals_id', $data['seller_deal_id'])
//            ->get()
//            ->row_array();

        $this->db->trans_start();

        //Отключаем за ненадобностью
//        $cash = $seller_data['dostupno_dlya_sdelok'] - $data['summa_sdelki'];
//        $this->db->where('for_deals_id', $data['seller_deal_id'])
//            ->update('companies', [
//                'dostupno_dlya_sdelok' => $cash > 0
//                    ? $cash
//                    : 0,
//            ]);
        //Создаем сделку
        $this->db->set('date', 'NOW()', false);
        $this->db->insert('deals', $data);

        //Берем ИД только что добавленной сделки
        $deal_id = $this->db->query('select LAST_INSERT_ID() as id')->row_array()['id'];

        $sum = 0;
        if($coupon_id > 0) {
            //Ставим, что купон использован
            $this->db->set('status', 1, false);
            $this->db->set('deal_id', $deal_id, false);
            $this->db->where('coupon_id', $coupon_id);
            $this->db->update('coupons');

            $sum = $this->db->select('summa')
                ->where('coupon_id', $coupon_id)
                ->limit(1)
                ->get('coupons')->row_array()['summa'];
        }

        $this->db->trans_complete();

        $sum = $data['summa_sdelki'] * (1 + PERCENT_SYSTEM / 100) - $sum;
        if ($sum < 0) $sum = 0;

        return [
            'status' => $this->db->trans_status(),
            'sum' => $sum
        ];
    }

    //обновляем статус сделки
    public function update_deal_status(
        $seller_deal_id,
        $buyer_deal_id,
        $deal_id,
        $status_deal
    ) {
        //если продавец отменил сделку,
        //просто апдейтим статус и все
        $deal = $this->db->where('deal_id', $deal_id)
            ->from('deals')->get()->row_array();
        if ($deal['seller_deal_id'] !== $seller_deal_id || $deal['buyer_deal_id'] !== $buyer_deal_id) {
            //Проверяем, отменяет ли сам покупатель
            if($deal['buyer_deal_id'] === $buyer_deal_id && $buyer_deal_id === $seller_deal_id){}
            else return false;
        }

        //Отмена сделки
        if ($status_deal == 0) {
            // Боунсы какие-то
            if ($buyer_deal_id === ADMIN_DEAL_ID) {
                $this->db->where('deal_id', $deal_id)
                    ->update('deals', [
                        'status_deal' => 0,
                    ]);

                return true;
            }
            // TODO продавцу лимит возвращается неправильно, т.к. если была сделка на большую сумму то лимит обнулится
            //  нужно как-то запоминать насколько увеличился лимит при совершении сделки
            $this->db->trans_start();

            //Данные по чату
            $this->db->select('vk.vk_chat_id AS chat_id,s.company_name AS company_name');
            $this->db->from('vk_notify vk');
            $this->db->join('companies b', 'b.for_deals_id=vk.user_deals_id');
            $this->db->join('companies s', 's.for_deals_id=s.for_deals_id');
            $this->db->where('s.for_deals_id', $seller_deal_id);
            $this->db->where('vk.user_deals_id', $buyer_deal_id);
            $data_chat = $this->db->get();

            //Меняем статус сделки на отмененную
            $data = [
                'status_deal' => $status_deal,
            ];
            $this->db->where('deal_id', $deal_id);
            $this->db->where('buyer_deal_id', $buyer_deal_id);
            $this->db->update('deals', $data);

            //Возвращаем купон, если таковой имеется
            $this->db->set('status', 0, false);
            $this->db->where('deal_id', $deal_id);
            $this->db->limit(1);
            $this->db->update('coupons');

            $this->db->trans_complete();

            if ($this->db->trans_status() !== false) {
                return $data_chat->row_array() ?? true;
            }

            return false;
        }

        if ($status_deal == 2) {
            //            // взять сумму сделки
            //            $sum_deal = $this->db
            //                ->select('summa_sdelki')
            //                ->where('deal_id', $deal_id)
            //                ->get('deals')
            //                ->row_array();
            //            // взять доступную сумму для сделок
            //            $sellerBalance = $this->db
            //                ->select('sverh_limit, dostupno_dlya_sdelok')
            //                ->where('for_deals_id', $seller_deal_id)
            //                ->get('companies')
            //                ->row_array();
            // проверить по % что он может принять сделку
            //начинаем транзакцию

            //В случаем, если покупатель - админ, только начисляем деньги продавцу.(Потому что админ царь и бог и он далек от мирских вещей)
            if ($buyer_deal_id === ADMIN_DEAL_ID || $buyer_deal_id === '5ecd1b9fbc3d497') {
                $this->db->trans_start();
                //Делаем сделку совершённой
                $this->db->where('deal_id', $deal_id)
                    ->update('deals', [
                        'status_deal' => $status_deal,
                    ]);
                //Получаем баланс продавца
                $res = $this->db
                    ->select('barter_balance')
                    ->where('for_deals_id', $seller_deal_id)
                    ->from('companies')
                    ->get()
                    ->row_array();
                //Апдейтим баланс
                $this->db->where('for_deals_id', $seller_deal_id)
                    ->update('companies', [
                        'barter_balance' => $res['barter_balance'] + $deal['summa_sdelki'],
                    ]);
                //Здесь купонов быть не может

                return $this->db->trans_complete();
            }

            //В стандартном случае обмена двух компаний
            $this->db->trans_start();

            $this->db->select('
                        t1.deal_id AS deal_id,
                        t1.seller_deal_id AS seller_deal_id,
                        t1.buyer_deal_id AS buyer_deal_id,
                        t1.summa_sdelki AS sum_deal,
                        t2.barter_balance AS buyer_barter_balance,
                        t2.company_name AS bayer_company_name,
                        t2.agent_id as buyer_agent_id,
                        t2.manager_id as buyer_manager_id,
                        t3.manager_id as seller_manager_id,
                        t3.company_name AS company_name,
                        t3.barter_balance AS seller_barter_balance,
                        t3.dostupno_dlya_sdelok AS seller_dostupno_dlya_sdelok,
                        wrk.barter_balance AS admin_barter_balance,
                        notify.vk_chat_id AS vk_chat
                    ');
            $this->db->from('deals t1');
            $this->db->join('companies t2', 't1.buyer_deal_id=t2.for_deals_id');
            $this->db->join('companies t3',
                't1.seller_deal_id=t3.for_deals_id');
            $this->db->join('workers AS wrk',
                'wrk.for_deals_id=wrk.for_deals_id');
            $this->db->join('vk_notify AS notify',
                'notify.user_deals_id=t1.buyer_deal_id', 'left');
            $this->db->where('t1.buyer_deal_id', $buyer_deal_id);
            $this->db->where('t1.deal_id', $deal_id);
            $this->db->where('wrk.for_deals_id', ADMIN_DEAL_ID);
            $this->db->limit(1);

            //Получаем детали сделки
            $deal_detail = $this->db->get()->row_array();

            //Получаем инфу по купону
            $this->db->where('deal_id', $deal_id);
            $this->db->limit(1);
            $coupon = $this->db->get('coupons');
            $coupon_sum = 0;
            $coupon_id = 0;

            if($coupon->num_rows() > 0) {
                $coupon = $coupon->row_array();
                $coupon_sum = $coupon['summa'];
                $coupon_id = $coupon['coupon_id'];
            }

            //считаем % сервиса от суммы сделки - на выходе у нас рубли!
            $percent_service = $deal_detail['sum_deal'] * (PERCENT_SYSTEM / 100) / 100;

            //здесь копейки
            $new_seller_balance = $deal_detail['seller_barter_balance']
                + $deal_detail['sum_deal'];

            //переводим в копейки
            $itog_sum_deal = $deal_detail['sum_deal'] + ($percent_service * 100) - $coupon_sum;
            if ($itog_sum_deal < 0) $itog_sum_deal = 0; //Если купон был больше суммы сделки с комиссией
            $new_buyer_balance = $deal_detail['buyer_barter_balance'] - $itog_sum_deal;

            // если представитель
            $buyer_manager = $this->db
                ->select('delegated_by')
                ->from('workers')
                ->where('for_deals_id', $deal_detail['buyer_manager_id'])
                ->get()
                ->row_array();
            //апдейт баланса покупателя
            $this->db->where('for_deals_id', $deal_detail['buyer_deal_id']);
            $this->db->update('companies',
                ['barter_balance' => $new_buyer_balance]);
            //апдейт баланса продавца
            $this->db->where('for_deals_id', $deal_detail['seller_deal_id']);
            $this->db->update('companies',
                ['barter_balance' => $new_seller_balance]);
            //апдейт статуса сделки
            $this->db->where('deal_id', $deal_id);
            $this->db->where('seller_deal_id', $deal_detail['seller_deal_id']);
            $this->db->update('deals', ['status_deal' => 2]);
            //апдейт статуса купона
            if($coupon_id > 0) {
                $this->db->set('status', 1, false);
                $this->db->where('coupon_id', $coupon_id);
                $this->db->limit(1);
                $this->db->update('coupons');
            }

            if ($buyer_manager['delegated_by'] !== '0') {
                $delegate = $this->db->select('for_deals_id, barter_balance')
                    ->where('user_id', $buyer_manager['delegated_by'])
                    ->from('workers')
                    ->get()
                    ->row_array();

                $add = $percent_service * 50;

                $new_admin_balance = $deal_detail['admin_barter_balance'] + $add;
                // update admin balance
                $this->db->where('for_deals_id', ADMIN_DEAL_ID);
                $this->db->update('workers',
                    ['barter_balance' => $new_admin_balance]);
                // update delegate balance
                $this->db->where('for_deals_id', $delegate['for_deals_id'])
                    ->update('workers',
                        ['barter_balance' => $delegate['barter_balance'] + $add]);
            } else {
                // если стандарт
                //переводим в копейки
                $new_admin_balance = $deal_detail['admin_barter_balance'] + ($percent_service * 100);

                //апдейт баланса админа
                $this->db->where('for_deals_id', ADMIN_DEAL_ID);
                $this->db->update('workers',
                    ['barter_balance' => $new_admin_balance]);
                $percentDeal = ($deal_detail['sum_deal'] * (PERCENT_SYSTEM / 100)) * AGENT_CASH_FROM_COMPANY_PAYMENTS;
                // update agent balance
                if (! empty($deal_detail['buyer_agent_id'])) {
                    $b_worker = $this->db->select('barter_balance, status')
                        ->where('user_id', $deal_detail['buyer_agent_id'])
                        ->get('workers')
                        ->row_array();

                    $this->db->where('for_deals_id', $b_worker['for_deals_id'])
                        ->update('workers', [
                            'barter_balance' => $b_worker['barter_balance'] + $percentDeal,
                        ]);

                }

            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                return false;
            }

            return [
                'chat_id' => $deal_detail['vk_chat'],
                'company_name' => $deal_detail['company_name'],
                'bayer_company_name' => $deal_detail['bayer_company_name'],
                'sum_deal' => $deal_detail['sum_deal'] / 100,
            ];
        }

        return false;
    }

    /**
     * Отмена сделки по обоюдному согласию сторон
     *
     * @param  string  $deal_id
     *
     * @return array|bool
     */
    public function mutuallyCancelDeal(string $deal_id)
    {
        $deal = $this->db->select('status_deal, want_cancel, seller_deal_id, buyer_deal_id, summa_sdelki')
            ->from('deals')
            ->where('deal_id', $deal_id)
            ->limit(1)->get()->row_array();
        $sessionId = $_SESSION['ses_company_data']['deals_id'];
        if ($deal['seller_deal_id'] !== $sessionId && $deal['buyer_deal_id'] !== $sessionId) {
            return [
                'status' => false,
                'message' => 'Ошибка в отмене сделки',
            ];
        }
        // Если пытаются отменить неподтвержденную сделку
        if ($deal['status_deal'] !== '2') {
            return false;
        }

        // Если вдруг попытаются отменить отмененную
        if ($deal['want_cancel'] === '2') {
            return [
                'status' => false,
                'message' => 'Сделка уже отменена',
            ];
        }

        // если покупатель отправляет заявку на отмену
        if ($deal['want_cancel'] === '0') {
            $this->db->where('deal_id', $deal_id)
                ->update('deals', [
                    'want_cancel' => 1,
                ]);

            return [
                'status' => true,
                'message' => 'Заявка на отмену успешно отправлена',
            ];
        }
        // Возвращаем деньги продавку и покупателю
        $participants = $this->db
            ->select('c1.company_id as seller_id, 
                                c1.barter_balance as seller_balance,
                                c1.dostupno_dlya_sdelok as dost_dlya_sdelok,
                                c1.manager_id as seller_manager, 
                                c2.company_id as buyer_id, 
                                c2.barter_balance as buyer_balance,
                                c2.manager_id as buyer_manager')
            ->from('deals d')
            ->where('deal_id', $deal_id)
            ->join('companies c1', 'd.seller_deal_id=c1.for_deals_id')
            ->join('companies c2', 'd.buyer_deal_id=c2.for_deals_id')
            ->get()->row_array();

        //Получаем инфу по купону
        $this->db->where('deal_id', $deal_id);
        $this->db->limit(1);
        $coupon = $this->db->get('coupons');
        $coupon_sum = 0;
        $coupon_id = 0;

        if($coupon->num_rows() > 0) {
            $coupon = $coupon->row_array();
            $coupon_sum = $coupon['summa'];
            $coupon_id = $coupon['coupon_id'];
        }

        if ((int) $participants['seller_balance'] < (int) $deal['summa_sdelki']) {
            return [
                'status' => false,
                'message' => 'Недостаточно средств',
            ];
        }
        $this->db->trans_start();
        // Отменяем сделку
        $this->db->where('deal_id', $deal_id)
            ->update('deals', [
                'status_deal' => 0,
                'want_cancel' => 2,
            ]);
        // Покупателю вернуть 100% от сделки + комиссию (* (1 + PERCENT_SYSTEM / 100))
        //Купон
        $itog_deal_sum = $deal['summa_sdelki'] * (1 + PERCENT_SYSTEM / 100) - $coupon_sum;
        if ($itog_deal_sum < 0) $itog_deal_sum = 0;
        $this->db->where('company_id', $participants['buyer_id'])
            ->update('companies', [
                'barter_balance' => $participants['buyer_balance'] + $itog_deal_sum,
            ]);

        //апдейт статуса купона
        if($coupon_id > 0) {
            $this->db->set('status', 0, false);
            $this->db->where('coupon_id', $coupon_id);
            $this->db->limit(1);
            $this->db->update('coupons');
        }

        // У продавца забрать 100% от сделки
        //        $dostypno_dlya_sdelok = ($participants['dost_dlya_sdelok'] + $deal['summa_sdelki']) > 0 ? ($participants['dost_dlya_sdelok'] + $deal['summa_sdelki']) : 0;
        $this->db->where('company_id', $participants['seller_id'])
            ->update('companies', [
                'barter_balance' => $participants['seller_balance'] - $deal['summa_sdelki'],
                //                'dostupno_dlya_sdelok' => $dostypno_dlya_sdelok,
            ]);
        $buyer_manager = $this->db->from('workers')->where('for_deals_id',
            $participants['buyer_manager'])->get()->row_array();

        $admin_balance = $this->db->select('barter_balance')
            ->from('workers')
            ->where('for_deals_id', ADMIN_DEAL_ID)
            ->get()->row_array();

        if ($buyer_manager['delegated_by'] !== '0') {
            $this->db->where('for_deals_id', ADMIN_DEAL_ID);
            $this->db->update('workers',
                ['barter_balance' => $admin_balance['barter_balance'] - ($deal['summa_sdelki'] * ((float) (PERCENT_SYSTEM / 2) / 100))]);

            $delegate = $this->db->where('user_id',
                $buyer_manager['delegated_by'])->from('workers')->get()->row_array();

            $this->db->where('user_id', $buyer_manager['delegated_by'])
                ->update('workers',
                    ['barter_balance' => $delegate['barter_balance'] - ($deal['summa_sdelki'] * ((float) (PERCENT_SYSTEM / 2) / 100))]);
        } else {
            // Забираем 5% от сделки у админа
            $this->db->where('for_deals_id', ADMIN_DEAL_ID);
            $this->db->update('workers',
                ['barter_balance' => $admin_balance['barter_balance'] - ($deal['summa_sdelki'] * (PERCENT_SYSTEM / 100))]);

            $agent_summa = ($deal['summa_sdelki'] * (PERCENT_SYSTEM / 100)) * AGENT_CASH_FROM_COMPANY_PAYMENTS;

            if ($participants['buyer_manager'] !== 'no') {
                $b_worker = $this->db->select('barter_balance')
                    ->from('workers')
                    ->where('for_deals_id', $participants['buyer_manager'])
                    ->get()
                    ->row_array();

                $this->db->where('for_deals_id', $participants['seller_manager'])
                    ->update('workers', [
                        'barter_balance' => $b_worker['barter_balance'] - $agent_summa,
                    ]);
            }
        }

        if ($this->db->trans_complete()) {
            return [
                'status' => true,
                'message' => 'Сделка успешно отменена!',
            ];
        }

        return [
            'status' => false,
            'message' => 'Что-то пошло не так...',
        ];
    }
}
