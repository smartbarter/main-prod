<?php

namespace Barter;

use YandexCheckout\Client;
use YandexCheckout\Request\Payments\CreatePaymentResponse;

class Payment
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var $this
     */
    private static $instance;

    private function __construct()
    {
        $this->client = new Client;
        $this->client->setAuth(SHOP_ID, SCID);
    }

    public static function getInstance(): Payment
    {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function createPayment(
        float $amount,
        string $type,
        string $description = null,
        array $metadata = []
    ): CreatePaymentResponse {
        return $this->client->createPayment(
            [
                'capture' => true,
                'amount' => [
                    'value' => $amount,
                    'currency' => 'RUB',
                ],

                'confirmation' => [
                    'type' => 'redirect',
                    'return_url' => site_url('company/payments/success_payment'),
                ],
                'description' => $description,

                'metadata' => array_merge(['type' => $type], $metadata),
            ],
            uniqid('354', true)
        );
    }
}
