<?php

class WhatsAppNotif extends WhatsAppAPI
{
    protected $company_phone = null;
    protected $curr_balance = '<ошибка>';
    protected $notif_enabled = 0;
    protected $notif_ap = 0;
    protected $notif_deal = 0;

    public function __construct($data, $type)
    {
        parent::__construct();
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load_company_data($data, $type);
    }

    public function load_company_data($data, $type)
    {
        $company_data = null;
        switch ($type) {
            case 'company_id':
                $company_data = $this->CModel->find_company_detail_by_id_ext($data);
                break;
            case 'company_phone':
                $company_data = $this->CModel->find_company_by_phone($data);
                break;
            case 'for_deals_id':
                $company_data = $this->CModel->find_company_data_by_deals_id($data);
                break;
            default:
                return false;
        }

        $this->company_phone = $company_data['company_phone'];
        $this->notif_enabled = $company_data['whatsapp_notif'];
        $this->notif_ap = $company_data['whatsapp_ap'];
        $this->notif_deal = $company_data['whatsapp_deal'];
        $this->curr_balance = $company_data['barter_balance'];
        return 1;
    }

    public function change_phone($phone)
    {
        $this->company_phone = $phone;
        $company_id = 0;
    }

    //Уведомление об окончании АП
    public function send_ap_expiration()
    {
        if (!$this->notif_enabled || !$this->notif_ap) return false;
        $text = "barter-business.ru: Уведомоляем Вас, о том, что срок действия Вашей подписки подошел к концу! Если хотите и дальше получать услуги по бартеру, пожалуйста, оплатите её! Ссылка на оплату: " . site_url('/company/abon_plata/');
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о поступившей сделке
    public function send_incoming_deal($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Вам поступила новая сделка от \"$from\" на сумму $sum БР! Зайдите во входящие сделки, чтобы принять, или отклонить её!"
            . " Текущий баланс: " . $this->curr_balance . " БР";
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о том, что отправленная сделка принята
    public function send_outgoing_deal_accepted($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Компания \"$from\" приняла Вашу сделку на сумму $sum БР!"
            . " Текущий баланс: " . $this->curr_balance . " БР";;
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о том, что отправленная сделка отклонена
    public function send_outgoing_deal_rejected($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Компания \"$from\" отклонила Вашу сделку на сумму $sum БР!"
            . " Текущий баланс: " . $this->curr_balance . " БР";;
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о том, что другая компания запросила возврат
    public function send_incoming_refund_requested($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Компания \"$from\" запросила возврат по сделке на сумму $sum БР! Зайдите во входящие сделки, чтобы принять, или отклонить запрос!"
            . " Текущий баланс: " . $this->curr_balance . " БР";;
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о том, что запрошенный возврат одобрен
    public function send_outgoing_refund_accepted($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Компания \"$from\" приняла запрос на возврат средств по сделке на сумму $sum БР!"
            . " Текущий баланс: " . $this->curr_balance . " БР";;
        return $this->sendMessage($this->company_phone, $text);
    }
    //Уведомление о том, что запрошенный возврат отклонен
    public function send_outgoing_refund_rejected($from, $sum)
    {
        if (!$this->notif_enabled || !$this->notif_deal) return false;
        $text = "barter-business.ru: Компания \"$from\" отклонила запрос на возврат средств по сделке на сумму $sum БР!"
            . " Текущий баланс: " . $this->curr_balance . " БР";;
        return $this->sendMessage($this->company_phone, $text);
    }
}