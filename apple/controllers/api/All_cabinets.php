<?php

use Barter\CashActions;
use Barter\SubscriptionService;
use YandexCheckout\Model\Notification\NotificationSucceeded;
use YandexCheckout\Model\NotificationEventType;

defined('BASEPATH') OR exit('No direct script access allowed');

class All_cabinets extends CI_Controller
{
//        public function __construct(){
//            parent::__construct();
//
//           if(empty($_POST)) {
//               redirect(base_url() . 'publics/page_not_found', 'location', 301);
//            }
//
//         }

    public function ymoney_process() {

        $json = $_POST;

        file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/apple/logs/transaction.log',
            date(DATE_RFC822).": Яндекс.Деньги: POST: " . json_encode($json) . PHP_EOL, FILE_APPEND);

        //Достаем контрольную сумму
        $checksum = $json['sha1_hash'];

        //Формируем строку
        $data = $json['notification_type'] . '&' .
            $json['operation_id'] . '&' .
            $json['amount'] . '&' .
            $json['currency'] . '&' .
            $json['datetime'] . '&' .
            $json['sender'] . '&' .
            $json['codepro'] . '&' .
            SECRET_YANDEX . '&' .
            $json['label'];

        //Вычисляем хэш
        $sha1 = sha1($data);

        if ($checksum != $sha1) {
            //Если данные не совпадают
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                date(DATE_RFC822).": Яндекс.Деньги: Ошибка хэша!" . PHP_EOL, FILE_APPEND);
            return;
        }

        //Ответ о успешном приеме
        $this->output->set_status_header(200)
            ->set_output(json_encode(['status' => 'ok'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES))
            ->_display();

        if ($json['test_notification'] == true) {
            file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/apple/logs/transaction.log',
                date(DATE_RFC822).": Яндекс.Деньги: Это тестовое уведомление!" . PHP_EOL, FILE_APPEND);
            return;
        }

        if ($json['unaccepted'] == "true") {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                date(DATE_RFC822)."Яндекс.Деньги: ID" . $json['label'] . " Ошибка! Платеж не прошел!" . PHP_EOL, FILE_APPEND);
            return;
        }

        if ($json['withdraw_amount'] != 500.00) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                date(DATE_RFC822)."Яндекс.Деньги: ID" . $json['label'] . " Ошибка суммы оплаты! Оплачено: " . $json['amount'] . ' р. Требуется: 500 р.' . PHP_EOL, FILE_APPEND);
            return;
        }
        if (!isset($json['label'])) {
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                date(DATE_RFC822)."Яндекс.Деньги: не указан ID компании!" . PHP_EOL, FILE_APPEND);
            return;
        }

        $this->load->model('company_cabinet/Payments_model', 'PModel');
        $this->load->model('publics/Company_model', 'CModel');

        //Активируем и заносим в БД
        $data = [
            'company_id' => (int)$json['label'],
            'type_payment' => 1,
            'summa' => (int)($json['withdraw_amount'] * 100),
        ];

        $this->PModel->yandex_payment_company_new($data, 28);
        file_put_contents($_SERVER['DOCUMENT_ROOT']
            .'/apple/logs/transaction.log',
            date(DATE_RFC822).": Яндекс.Деньги: ID:" . $json['label'] . " Оплачено: " . $json['amount'] . ' р.' . PHP_EOL, FILE_APPEND);
    }

    public function process_payment() {
        //Получаем данные
        $json = $_GET;
        file_put_contents($_SERVER['DOCUMENT_ROOT'] .'/apple/logs/transaction.log',
            date(DATE_RFC822).": Сбербанк: POST: " . json_encode($json) . PHP_EOL, FILE_APPEND);
        //Достаем контрольную сумму
        $checksum = $json['checksum'];
        unset($json['checksum']);
        //Оставшийся массив сотрируем по возрастанию
        ksort($json);
        //Формируем строку
        $data = '';
        foreach ($json as $key => $val) {
            $data .= $key . ';' . $val . ';';
        }
        //Вычисляем хэш
        $hmac = mb_strtoupper(hash_hmac( 'sha256' , $data , SBER_HASH_KEY));
        if ($checksum !== $hmac) {
            //Если данные не совпадают
            file_put_contents($_SERVER['DOCUMENT_ROOT']
                .'/apple/logs/transaction.log',
                date(DATE_RFC822).": Сбербанк: Ошибка хэша!" . PHP_EOL, FILE_APPEND);
            return;
        }
        //Ответ о успешном приеме
        $this->output->set_status_header(200)
            ->set_output(json_encode(['status' => 'ok'],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES))
            ->_display();

        $this->load->model('company_cabinet/Payments_model', 'PModel');
        $this->load->model('publics/Company_model', 'CModel');

        switch ($json['operation']) {
            case 'reversed': //операция отмены
            case 'refunded': //операция возврата
                if($json['status'] == 0) return;
                $this->PModel->change_payment_status($json['orderNumber'], 2);
                break;

            case 'deposited':
                if($json['status'] == 0) return;
                $payment_status = $this->PModel->get_payment_status($json['orderNumber']);
                if ($payment_status != 0) {
                    file_put_contents($_SERVER['DOCUMENT_ROOT']
                        . '/apple/logs/transaction.log',
                        date(DATE_RFC822) . ": Ошибка: Компания ID " . $data['company_id'] .  '. Оплата ' . $data['summa'] / 100 . 'р. Ошибка смены статуса на оплачено(1)! Текущий статус: ' . $payment_status
                        . PHP_EOL, FILE_APPEND);
                    return;
                }

                $data = [
                    'company_id' => $json['company_id'],
                    'payment_id' => $json['orderNumber'],
                    'summa' => $json['amount']
                ];
                switch ($json['type_payment']) {

                    case UPDATE_BALANCE:
                        $this->CModel->update_balance($json['company_id'],
                            $json['amount']);
                        break;

                    case MONTHLY_PAYMENT:
                        $this->PModel->sberbank_payment_company($data, 28);
                        break;

                    case PAYMENT_THREEMONTH:
                        // TODO % агенту
                        $summa = $json['amount'] + 75 * 100;

                        $this->PModel->sberbank_payment_company($data, 30 * 3);
                        //Бонусные бартерные рубли через сделку
                        $this->CModel->createDealFromAdmin($json['company_id'], (int)($json['amount'] * 0.3), 'Бонус за единовременную оплату 3 месяцев');

                        break;

                    case PAYMENT_SIXMONTH:
                        $summa = $json['amount'] + 300 * 100;

                        $this->PModel->sberbank_payment_company($data, 30 * 6);
                        //Бонусные бартерные рубли через сделку
                        $this->CModel->createDealFromAdmin($json['company_id'], (int)($json['amount'] * 0.5), 'Бонус за единовременную оплату 6 месяцев');
                        break;

                    case PAYMENT_TWELVEMONTH:
                        $summa = $json['amount'] + 900 * 100;

                        $this->PModel->sberbank_payment_company($data, 30 * 12);
                        //Бонусные бартерные рубли через сделку
                        $this->CModel->createDealFromAdmin($json['company_id'], (int)($json['amount'] * 1), 'Бонус за единовременную оплату 12 месяцев');
                        break;

//                case PAYMENT_VIP:
//                    $data = [
//                        'company_id' => $payment_meta['company_id'],
//                        'type_payment' => 1,
//                        'summa' => COST_SERVICE * 100,
//                    ];
//                    $this->PModel->yandex_payment_company($data);
//                    break;
                } //End $json['type_payment']
                break;
            default:
                return;
        }
    }

    public function yandex_process()
    {

        $source = file_get_contents('php://input');
        $json = json_decode($source, true);
        try {
            if ($json['event'] === YandexCheckout\Model\NotificationEventType::PAYMENT_SUCCEEDED) {
                $notification = new NotificationSucceeded($json);
            } else {
                return $this->output->set_status_header(400)->_display();
            }

            $payment = $notification->getObject();
            $payment_meta = $payment->getMetadata()->toArray();

            $this->load->model('company_cabinet/Payments_model', 'PModel');
            $this->load->model('publics/Company_model', 'CModel');
            $sub = new SubscriptionService($payment_meta['company_id']);
            $cash = new CashActions($payment_meta['company_id']);
            switch ($payment_meta['type']) {

                case UPDATE_BALANCE:
                    $this->CModel->update_balance($payment_meta['company_id'],
                        $payment->getAmount()->getIntegerValue());
                    break;

                case MONTHLY_PAYMENT:
                    $data = [
                        'company_id' => $payment_meta['company_id'],
                        'type_payment' => 1,
                        'summa' => $payment->getAmount()->getIntegerValue(),
                    ];
                    $this->PModel->yandex_payment_company_new($data, 28);
                    break;

                case PAYMENT_THREEMONTH:
                    // TODO % агенту
                    $summa = $payment->getAmount()->getIntegerValue() + 75 * 100;

                    $data = [
                        'company_id' => $payment_meta['company_id'],
                        'type_payment' => 1,
                        'summa' => $payment->getAmount()->getIntegerValue(),
                    ];
                    $this->PModel->yandex_payment_company_new($data, 30 * 3);
                    //Бонусные бартерные рубли через сделку
                    $this->CModel->createDealFromAdmin($payment_meta['company_id'], (int)($payment->getAmount()->getIntegerValue() * 0.3), 'Бонус за единовременную оплату 3 месяцев');

                    break;

                case PAYMENT_SIXMONTH:
                    $summa = $payment->getAmount()->getIntegerValue() + 300 * 100;

                    $data = [
                        'company_id' => $payment_meta['company_id'],
                        'type_payment' => 1,
                        'summa' => $payment->getAmount()->getIntegerValue(),
                    ];
                    $this->PModel->yandex_payment_company_new($data, 30 * 6);
                    //Бонусные бартерные рубли через сделку
                    $this->CModel->createDealFromAdmin($payment_meta['company_id'], (int)($payment->getAmount()->getIntegerValue() * 0.5), 'Бонус за единовременную оплату 6 месяцев');
                    break;

                case PAYMENT_TWELVEMONTH:
                    $summa = $payment->getAmount()->getIntegerValue() + 900 * 100;

                    $data = [
                        'company_id' => $payment_meta['company_id'],
                        'type_payment' => 1,
                        'summa' => $payment->getAmount()->getIntegerValue(),
                    ];
                    $this->PModel->yandex_payment_company_new($data, 30 * 12);
                    //Бонусные бартерные рубли через сделку
                    $this->CModel->createDealFromAdmin($payment_meta['company_id'], (int)($payment->getAmount()->getIntegerValue() * 1), 'Бонус за единовременную оплату 12 месяцев');
                    break;

//                case PAYMENT_VIP:
//                    $data = [
//                        'company_id' => $payment_meta['company_id'],
//                        'type_payment' => 1,
//                        'summa' => COST_SERVICE * 100,
//                    ];
//                    $this->PModel->yandex_payment_company($data);
//                    break;
            }

            $this->output->set_status_header(200)
                ->set_output(json_encode(['status' => 'ok'],
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES))
                ->_display();
        } catch (Exception $e) {
            // Обработка ошибок при неверных данных
        }
    }
}
