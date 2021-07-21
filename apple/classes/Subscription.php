<?php
namespace Barter;

use YandexCheckout\Request\Payments\CreatePaymentResponse;

class Subscription
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var int
     */
    private $company_id;

    public function __construct(string $type, int $company_id)
    {
        $this->type = $type;
        $this->company_id = $company_id;
    }

    public function createPayment(): CreatePaymentResponse
    {
        [$amount, $tariff] = $this->defineAmount();
        //Вариант 2
//        $a = $this->defineAmount();
//        $amount = $a[0];
//        $tariff = $a[1];


        return Payment::getInstance()->createPayment($amount, $this->type,
            sprintf('Пополнение абонентской платы (%s) : ID %s', $tariff, $this->company_id),
            ['company_id' => $this->company_id]);
    }

    private function defineAmount(): array
    {
        switch ($this->type) {
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
