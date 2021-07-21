<?php

namespace Barter;

class SubscriptionSberbank
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var int
     */
    private $company_id;
    /**
     * @var string
     */
    private $message;
    /**
     * @var string
     */
    private $amount;

    public function __construct(string $type, int $company_id, int $amount = null, string $message = null)
    {
        $this->type = $type;
        $this->company_id = $company_id;
        $this->amount = $amount;
        $this->message = $message;
    }

    public function createPayment($payment_id)
    {
        [$amount, $tariff] = $this->defineAmount();
        if ($amount === 0 || $amount === null) {
            return [
                'status' => false,
                'errorCode' => -1,
                'errorMessage' => 'Не указана сумма платежа!'
            ];
        }
        //Вариант 2
//        $a = $this->defineAmount();
//        $amount = $a[0];
//        $tariff = $a[1];
        //Создаем ссылку на оплату
        $url = 'https://3dsec.sberbank.ru/payment/rest/register.do?';
        $options = [
            //'token' => SBER_TOKEN,
            'userName' => 'barter-api',
            'password' => 'barter',
            'amount' => $amount * 100,
            'returnUrl' => base_url() . 'company/payments/success_payment',
            'failUrl' => base_url() . 'company/payments/fail_payment',
            'orderNumber' => $payment_id,
            'jsonParams' => sprintf('{"company_id":%s,"type_payment":"%s","amount":%s}', $this->company_id, $this->type, $amount * 100)
        ];
        if ($this->message === null) {
            $options += [
                'description' => sprintf('Пополнение абонентской платы (%s) : ID %s', $tariff, $this->company_id)
            ];
        }
        else {
            $options += [
                'description' => sprintf('ID %s: %s', $this->company_id, $this->message)
            ];
        }
        $url .= http_build_query($options,'','&');
        //Выполняем запрос
        $result = json_decode(file_get_contents($url), true);
        //Обрабатываем ошибки
        if (isset($result['errorCode'])) {
            log_message('error', sprintf('Ошибка оплаты ID %s (%s). ID заказа: %s Код ошибки: %s. Сообщение: %s',
                $this->company_id,
                $tariff,
                $payment_id,
                $result['errorCode'],
                $result['errorMessage']));

            return [
                'status' => false,
                'errorCode' => $result['errorCode'],
                'errorMessage' => $result['errorMessage']
            ];
        }

        return [
            'status' => true,
            'orderId' => $result['orderId'],
            'formUrl' => $result['formUrl'],
            'amount' => $amount * 100
        ];
    }

    private function defineAmount(): array
    {
        switch ($this->type) {
            case UPDATE_BALANCE:
                return [$this->amount, $this->message];
            case MONTHLY_PAYMENT:
                return [500, 'за месяц'];
            case PAYMENT_THREEMONTH:
                return [1500, 'за 3 месяца'];
            case PAYMENT_SIXMONTH:
                return [3000, 'за 6 месяцев'];
            case PAYMENT_TWELVEMONTH:
                return [6000, 'за 12 месяцев'];
            case PAYMENT_VIP:
                return [5000, 'VIP'];
            default:
                return [0, ''];
        }
    }
}