<?php

use Barter\Payment;
use Barter\SubscriptionSberbank;
use Barter\Subscription;
use Barter\CustomException;
use YandexCheckout\Client;

defined('BASEPATH') or exit('No direct script access allowed');

class Ajax extends CI_Controller
{
    //функция изменения работы компании сверх лимита
    //В выпадающем списке человек выбирает одну из опций
    //Работаю, по договоренности, не работаю
    public function change_work_sverh_limit()
    {
        $this->load->model('company_cabinet/Company_model', 'CModel');

        $this->load->library('form_validation');//загружаем библиотеку валидации формы

        if ($this->form_validation->run('work_sverh_limit') == false) {
            $array_errors = [
                'sverh_limit' => form_error('sverh_limit'),
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        } else {
            //если валидацию прошли, меняем данные в БД

            $result = $this->CModel->change_work_sverh_limit(
                $_SESSION['ses_company_data']['company_id'],
                $this->input->post('sverh_limit')
            );

            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Все данные успешно сохранены!',
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Что-то пошло не так, попробуйте еще раз!',
                    ]));
            }
        }
    }

    public function create_new_deal()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $company_id = $_SESSION['ses_company_data']['company_id'];

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $result = $this->CModel->check_sub_status($company_id);

        try
        {
            if ($result < 0) {
                throw new CustomException('Ошибка проверки статуса оплаты АП!');
            } elseif ($result != 1) {
                throw new CustomException('Абоненская плата не оплачена!', ['sub_status' => 0]);
            }

            $this->load->library('form_validation');//загружаем библиотеку валидации формы
            if ($this->form_validation->run('sum_new_deal_light') == false) {
                $array_errors = [
                    'sum_deal' => form_error('sum_deal'),
                    'comment_deal' => form_error('comment_deal'),
                    'coupon' => form_error('coupon'),
                ];
                throw new CustomException('Ошибка валидации!', ['response_data' => $array_errors]);

            }
            else
                {
                $this->load->model('all_areas/Deals_model', 'DModel');

                $sum_deal = abs($this->input->post('sum_deal'));//приходят копейки
                $comment = $this->input->post('comment_deal');
                $coupon_id = $this->input->post('coupon');


                $this->load->library('user_agent');

                $seller_deal_id = null;
                $seller_data = null;
                if ($this->agent->is_mobile()) {
                    //В случае если пользователь с мобильного, можем получить только id компании
                    $seller_id = $this->input->post('company_id');
                    $seller_data = $this->CModel->find_company_detail_by_id($seller_id);
                    $seller_deal_id = $seller_data['for_deals_id'];
                } else {
                    //В случае если пользователь с ПК, получаем сразу deal_id
                    $seller_data = $this->CModel->find_company_and_manager($this->input->post('seller_deal_id'));
                    $seller_deal_id = $this->input->post('seller_deal_id');
                }

                //Получаем информацию по купону
                $coupon_sum = 0; //Сумма купона
                if ($coupon_id > 0) {
                    $coupon = $this->CModel->get_coupon($coupon_id, $_SESSION['ses_company_data']['company_id']);
                    if (!isset($coupon) || $coupon['status'] != 0) {
                        throw new CustomException('Используется некорректный купон!');
                    }
                    $coupon_sum = $coupon['summa'];
                } else {
                    $coupon_id = 0;
                }

                //Считаем итоговую сумму с комиссией
                $itog_sum = $sum_deal * (1 + PERCENT_SYSTEM / 100) - $coupon_sum;
                if ($itog_sum < 0) $itog_sum = 0;

                $user_data = $this->CModel->find_company_detail_by_id($_SESSION['ses_company_data']['company_id']);

                if ($user_data['barter_balance'] < $itog_sum) {
                    throw new CustomException('Сумма сделки превышает ваш баланс!');
                }

                $insert_data = [
                    'seller_deal_id' => $seller_deal_id,
                    'buyer_deal_id' => $_SESSION['ses_company_data']['deals_id'],
                    'summa_sdelki' => $sum_deal,
                    'comment_deal' => $comment,
                ];

                if ($seller_deal_id === $_SESSION['ses_company_data']['deals_id']) {
                    throw new CustomException('Похоже, Вы пытаетесь купить сами у себя!(Err1)');
                }

                $result = $this->DModel->create_new_deal($insert_data, $coupon_id);

                if ($result['status'] == false) {
                    throw new CustomException('Ошибка создания сделки! Обновите страницу и попробуйте еще раз!');
                }

                $this->load->library('vkbot');
                $this->load->model('publics/VK_bot_model', 'VKModel');

                $chat_id = $this->VKModel->find_vk_chat_id($seller_deal_id);

                if ($chat_id) {
                    //отправляем уведомление продавцу, что поступила новая заявка на сделку
                    $this->vkbot->send_text_message(
                        'Здравствуйте! Поступила новая заявка на сделку, на сумму '
                        . $sum_deal / 100
                        . ' руб., зайдите в личный кабинет. С уважением, '
                        . PROJECT_NAME . '!',
                        $chat_id['vk_chat_id']
                    );
                }
                //Уведомление о поступившей сделке в WhatsApp
                //$wa = new WhatsAppNotif($seller_data['company_id'], 'company_id');
                //$wa->send_incoming_deal($user_data['company_name'],$sum_deal / 100);

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Сделка создана! Оставьте комментарий о компании!',
                        'sum' => $result['sum']
                    ]));
            }
        }
        catch (CustomException $e)
        {
            $init_array = [
                'status' => 'fail',
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => $e->getMessage(),
            ];
            if (!empty($e->getOptions())) {
                $init_array = array_merge($init_array, $e->getOptions());
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($init_array));
        }
        return false;
    }

    public function create_new_deal_old()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $company_id = $_SESSION['ses_company_data']['company_id'];

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $result = $this->CModel->check_sub_status($company_id);

        if ($result < 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка проверки статуса оплаты АП!',
                ]));
            return;
        } elseif ($result != 1) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'sub_status' => 0,
                    'text_message' => 'Абоненская плата не оплачена!',
                ]));
            return;
        }

        $this->load->library('form_validation');//загружаем библиотеку валидации формы
        if ($this->form_validation->run('sum_new_deal_light') == false) {
            $array_errors = [
                'sum_deal' => form_error('sum_deal'),
                'comment_deal' => form_error('comment_deal'),
                'coupon' => form_error('coupon'),
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка валидации!',
                ]));
        } else {
            $this->load->model('all_areas/Deals_model', 'DModel');

            $sum_deal = abs($this->input->post('sum_deal'));//приходят копейки
            $comment = $this->input->post('comment_deal');
            $coupon_id = $this->input->post('coupon');

            $seller_deal_id = null;
            $this->load->library('user_agent');
            if($this->agent->is_mobile()) {
                //В случае если пользователь с мобильного, можем получить только id компании
                $seller_id = $this->input->post('company_id');
                $seller_deal_id = $this->CModel->find_company_detail_by_id($seller_id)['for_deals_id'];
            }
            else {
                //В случае если пользователь с ПК, получаем сразу deal_id
                $seller_deal_id = $this->input->post('seller_deal_id');
            }

            //Получаем информацию по купону
            $coupon_sum = 0; //Сумма купона
            if ($coupon_id > 0) {
                $coupon = $this->CModel->get_coupon($coupon_id, $_SESSION['ses_company_data']['company_id']);
                if (!isset($coupon) || $coupon['status'] != 0) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Использован некорректный купон!',
                        ]));
                    return;
                }
                $coupon_sum = $coupon['summa'];
            }
            else {
                $coupon_id = 0;
            }

            //Считаем итоговую сумму с комиссией
            $itog_sum = $sum_deal * (1 + PERCENT_SYSTEM / 100) - $coupon_sum;
            if ($itog_sum < 0) $itog_sum = 0;

            $user_data = $this->CModel->find_company_detail_by_id($_SESSION['ses_company_data']['company_id']);

            if ($user_data['barter_balance'] < $itog_sum)
            {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Сумма сделки превышает ваш баланс!',
                    ]));

                return;
            }

            $insert_data = [
                'seller_deal_id' => $seller_deal_id,
                'buyer_deal_id' => $_SESSION['ses_company_data']['deals_id'],
                'summa_sdelki' => $sum_deal,
                'comment_deal' => $comment,
            ];

            if ($seller_deal_id === $_SESSION['ses_company_data']['deals_id']) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Что-то пошло не так, попробуйте еще раз!(Err1)',
                    ]));

                return;
            }

            $result = $this->DModel->create_new_deal($insert_data, $coupon_id);
            if ($result['status']) {
                $this->load->library('vkbot');
                $this->load->model('publics/VK_bot_model', 'VKModel');

                $chat_id = $this->VKModel->find_vk_chat_id($seller_deal_id);

                if ($chat_id) {
                    //отправляем уведомление продавцу, что поступила новая заявка на сделку
                    $this->vkbot->send_text_message(
                        'Здравствуйте! Поступила новая заявка на сделку, на сумму '
                        . $sum_deal / 100
                        . ' руб., зайдите в личный кабинет. С уважением, '
                        . PROJECT_NAME . '!',
                        $chat_id['vk_chat_id']
                    );
                }

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Сделка создана! Оставьте комментарий о компании!',
                        'sum' => $result['sum']
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Что-то пошло не так, попробуйте еще раз!(Err2)',
                    ]));
            }
        }
    }
    //функция апдейта статуса сделки
    //0 - отменена
    //1 - ожидает подтверждения от продавца
    //2 - совершена
    public function update_status_deal()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('all_areas/Deals_model', 'DModel');

        $this->load->library('form_validation');//загружаем библиотеку валидации формы

        if ($this->form_validation->run('update_deal_status') == false) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail_data',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'message' => 'Нехуй менять данные!',
                ]));
        } else {
            //ловим данные
            $buyer_deal_id = $this->input->post('buyer_deal_id');
            $deal_id = $this->input->post('deal_id');
            $new_status_deal = $this->input->post('status_deal');

            //проверяем статус сделки, не отменил ли ее покупатель уже
            //или быть может уже продавец подтвердил ее, а покупатель захотел отменить?
            $status_deal = $this->DModel->get_status_deal($buyer_deal_id, $deal_id);

            if ($status_deal == 0) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'message' => 'Увы, но сделку отменили!',
                    ]));

                return;
            }

            if ($status_deal == 2) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'message' => 'Увы, но сделка уже принята!',
                    ]));

                return;
            }

            //пишем их в БД
            $result = $this->DModel->update_deal_status(
                $_SESSION['ses_company_data']['deals_id'],
                $buyer_deal_id,
                $deal_id,
                $new_status_deal
            );

            if ($result) {
                //если сделку отменяет покупатель - мы не отправляем уведомлений
                if ($_SESSION['ses_company_data']['deals_id'] != $buyer_deal_id) {
                    $this->load->library('vkbot');

                    //$self =  $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);
                    //$deal = $this->DModel->get_deal_info($deal_id);
                    //$wa = new WhatsAppNotif($buyer_deal_id, 'for_deals_id');
                    switch ($new_status_deal) {
                        case 0:
                            //если продавец отменил сделку
                            //$wa->send_outgoing_deal_rejected($self['company_name'], $deal['summa'] / 100);
                            $txt = 'Здравствуйте! Компания «'
                                . $result['company_name']
                                . '» отменила сделку. С уважением, ' . PROJECT_NAME
                                . '!';
                            break;

                        case 2:
                            //если продавец подтвердил сделку
                            //$wa->send_outgoing_deal_accepted($self['company_name'], $deal['summa'] / 100);
                            $txt = 'Здравствуйте! Компания «'
                                . $result['company_name']
                                . '» подтвердила сделку. С уважением, '
                                . PROJECT_NAME . '!';

                            //шлем админу сообщение в ВК, что совершена сделка
                            //                            $admin_msg = 'Прошла сделка на сумму '
                            //                                .$result['sum_deal'].'руб. Продавец «'
                            //                                .$result['company_name'].'», покупатель «'
                            //                                .$result['bayer_company_name'].'»';
                            //                            $this->vkbot->send_text_message($admin_msg,
                            //                                ADMIN_VK_ID);
                            break;
                    }
                    //отправляем сообщение покупателю или продавцу
                    $this->vkbot->send_text_message($txt, $result['chat_id']);
                }

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'message' => 'Все данные успешно обновлены!',
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'message' => 'Что-то пошло не так, мы не смогли обновить данные! Проверьте свой баланс и процент лимита',
                    ]));
            }
        }
    }

    //функция создания скидки
    public function create_discount()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules('date', 'дата',
            'required|regex_match[/\d{4}(-|\/)\d{2}(-|\/)\d{2}/]');
        $this->form_validation->set_rules('skidka', 'скидка',
            'required|numeric|integer');

        if ($this->form_validation->run() == false) {
            $array_errors = [
                'date' => form_error('date'),
                'skidka' => form_error('skidka'),
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        } else {
            $this->load->model('all_areas/Discounts_model', 'DISCModel');

            $skidka = $this->input->post('skidka');
            $date = $this->input->post('date');

            $data_insert = [
                'company_id' => $_SESSION['ses_company_data']['company_id'],
                'summa_skidki' => $skidka,
                'end_date' => $date . ' 23:59:59',
            ];

            //делаем проверку, нет ли у нас скидки на эту дату

            $find_discount
                = $this->DISCModel->find_discount_on_date($data_insert['company_id'],
                $data_insert['end_date']);

            if ($find_discount) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Ой.. Вы уже запланировали на эту дату скидку...',
                    ]));

                return;
            }

            $result = $this->DISCModel->save_new_discount($data_insert);

            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Скидка успешно сохранена, сейчас эта страница обновится!',
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Ой.. Мы не смогли сохранить вашу скидку..',
                    ]));
            }
        }
    }

    //функция удаления скидки
    public function delete_discount()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $discount_id = (int)$this->input->post('discount_id');

        if (!$discount_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'message' => 'Вы подменили данные!',
                ]));

            return;
        }

        $this->load->model('all_areas/Discounts_model', 'DISCModel');

        $result = $this->DISCModel->delete_discount($discount_id,
            $_SESSION['ses_company_data']['company_id']);

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'message' => 'Скидка удалена, сейчас эта страница обновится!',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'message' => 'Что-то пошло не так, скидка не удалена!',
                ]));
        }
    }

    public function update_profile()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->library('form_validation');

        if ($this->form_validation->run('update_company_data') === false) {
            $array_errors = [
                'company_name' => form_error('company_name'),
                'contact_name' => form_error('contact_name'),
                'contact_phone' => form_error('contact_phone'),
                'company_adress' => form_error('company_adress'),
                'company_site' => form_error('company_site'),
                'password' => form_error('password'),
                'city_name' => form_error('city_name'),
                'month_limit' => form_error('month_limit'),
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        } else {
            $this->load->model('company_cabinet/Company_model', 'CModel');
            $month_limit = ($this->input->post('month_limit') * 100);

            $new_value = $this->CModel->get_new_dostypno_dlya_sdelok((int)$_SESSION['ses_company_data']['company_id'],
                $month_limit);
            //ловим данные
            $new_data = [
                'company_name' => $this->input->post('company_name'),
                'contact_name' => $this->input->post('contact_name'),
                'adress' => $this->input->post('company_adress'),
                'company_site' => $this->input->post('company_site'),
                'month_limit' => $month_limit,
                'dostupno_dlya_sdelok' => $new_value,
                'social_vk' => $this->input->post('social_vk'),
                'social_inst' => $this->input->post('social_inst'),
            ];

            if (!empty($this->input->post('name_city_company'))) $new_data += ['city_name' => $this->input->post('name_city_company')];
            if (!empty($this->input->post('id_city_company_kladr'))) $new_data += ['city_code' => $this->input->post('id_city_company_kladr')];

            if (!empty($this->input->post('contact_phone'))) {
                $company_phone = $this->input->post('contact_phone');

                //проверяем, есть ли такой номер телефона в базе
                $find_phone_number
                    = $this->CModel->find_company_by_phone($company_phone);

                if ($find_phone_number) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Такой номер телефона уже зарегистрирован в системе!',
                        ]));

                    return;
                }

                $new_data += [
                    'company_phone' => $company_phone,
                ];
            }

            if (!empty($this->input->post('password'))) {
                //Подгружаем нашу библиотеку шифрования
                $this->load->library('myencrypt');

                $new_data += [
                    'password' => $this->myencrypt->password_encrypt($this->input->post('password')),
                ];
            }

            //пишем данные в БД
            $result
                = $this->CModel->update_company_data($_SESSION['ses_company_data']['company_id'],
                $new_data);

            //Выходим из всех остальных сессий, если меняли пароль
            if ($result && !empty($this->input->post('password'))) {
                $this->close_sessions();
            }

            $result2 = $this->CModel->update_company_geocode($_SESSION['ses_company_data']['company_id']) ? "Положение на карте успешно обновлено." : "Положение на карте обновить не удалось!";

            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => "Все данные успешно обновлены, сейчас страница перезагрузится!\n$result2",
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => "Увы, но нам не удалось сохранить новые данные!\n$result2",
                    ]));
            }
        }
    }

    public function update_profile_m()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $form_type = $this->input->post('form_type');

        if (empty($form_type)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка формы! Обратитесь в техподдержку!',
                ]));
            return;
        }

        $validation_errors = false;
        $array_errors = [];

        $this->load->library('form_validation');
        $this->load->model('company_cabinet/Company_model', 'CModel');

        switch ($form_type) {
            case 'limit':
                if ($this->form_validation->run('update_company_data_limit') === false) {
                    $array_errors = [
                        'month_limit' => form_error('month_limit'),
                        'sverh_limit' => form_error('sverh_limit'),
                    ];
                    $validation_errors = true;
                    break;
                }

                $month_limit = ($this->input->post('month_limit') * 100);

                $new_value = $this->CModel->get_new_dostypno_dlya_sdelok((int)$_SESSION['ses_company_data']['company_id'],
                    $month_limit);

                $new_data = [
                    'month_limit' => $month_limit,
                    'sverh_limit' => $this->input->post('sverh_limit'),
                    'dostupno_dlya_sdelok' => $new_value,
                ];

                break;

            case 'comp_data':
                if ($this->form_validation->run('update_company_data_light') === false) {
                    $array_errors = [
                        'company_name' => form_error('company_name'),
                        'contact_name' => form_error('contact_name'),
                        'contact_phone' => form_error('contact_phone'),
                        'company_adress' => form_error('company_adress'),
                        'city_name' => form_error('city_name'),
                    ];
                    $validation_errors = true;
                    break;
                }

                $new_data = [
                    'company_name' => $this->input->post('company_name'),
                    'contact_name' => $this->input->post('contact_name'),
                    'adress' => $this->input->post('company_adress'),
                ];

                if (!empty($this->input->post('contact_phone'))) {
                    $company_phone = $this->input->post('contact_phone');

                    //проверяем, есть ли такой номер телефона в базе
                    $find_phone_number
                        = $this->CModel->find_company_by_phone($company_phone);

                    if ($find_phone_number) {
                        if ($find_phone_number['company_id'] != $_SESSION['ses_company_data']['company_id']) {
                            $this->output
                                ->set_content_type('application/json')
                                ->set_output(json_encode([
                                    'status' => 'fail',
                                    'csrf_token' => $this->security->get_csrf_hash(),
                                    'text_message' => 'Такой номер телефона уже зарегистрирован в системе!',
                                ]));
                            return;
                        }
                    }

                    $new_data += [
                        'company_phone' => $company_phone,
                    ];
                }

                if (!empty($this->input->post('city_name'))) $new_data += ['city_name' => $this->input->post('city_name')];
                if (!empty($this->input->post('id_city_company_kladr'))) $new_data += ['city_code' => $this->input->post('id_city_company_kladr')];

                break;

            case 'password':
                if ($this->form_validation->run('update_company_data_password') === false) {
                    $array_errors = [
                        'password1' => form_error('password1'),
                        'password2' => form_error('password2'),
                    ];
                    $validation_errors = true;
                    break;
                }

                if (!empty($this->input->post('password1')) && !empty($this->input->post('password2'))) {

                    if ($this->input->post('password1') !== $this->input->post('password2')) {
                        $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode([
                                'status' => 'fail',
                                'csrf_token' => $this->security->get_csrf_hash(),
                                'text_message' => 'Пароли не совпадают!',
                            ]));
                        return;
                    }
                    //Подгружаем нашу библиотеку шифрования
                    $this->load->library('myencrypt');

                    $new_data = [
                        'password' => $this->myencrypt->password_encrypt($this->input->post('password1')),
                    ];
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Пароль не может быть пустым!',
                        ]));
                    return;
                }

                break;

            case 'social':
                $new_data = [
                    'company_site' => $this->input->post('company_site'),
                    'social_vk' => $this->input->post('social_vk'),
                    'social_inst' => $this->input->post('social_inst'),
                ];
                break;

            default:
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Ошибка типа формы!',
                    ]));
                return;
        }

        if ($validation_errors) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка введенных данных!',
                ]));
            return;
        }

        $result = $this->CModel->update_company_data($_SESSION['ses_company_data']['company_id'],
            $new_data);

        //Выходим из всех остальных сессий, если меняли пароль
        if ($result && $form_type == 'password') {
            $this->close_sessions();
        }

        if ($form_type == 'comp_data') {
            $result_geocode = $this->CModel->update_company_geocode($_SESSION['ses_company_data']['company_id']) ?
                "Положение на карте успешно обновлено." :
                "Положение на карте обновить не удалось!";
        }
        else {
            $result_geocode = '';
        }

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Данные успешно обновлены, сейчас страница перезагрузится! $result_geocode",
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Увы, но нам не удалось сохранить новые данные! $result_geocode",
                ]));
        }
    }

    public function update_logo()
    {
        if (!empty($_FILES)) {
            //если файл прикреплен, подготавливаем конфиг и грузим файл
            $config['upload_path'] = './uploads/companys_logo/';//куда грузим
            $config['allowed_types'] = 'gif|jpg|jpeg|png';//допустимые форматы
            $config['max_size'] = 2048;//указываем в KB
            // $config['max_width']        = 500;//указываем в px
            // $config['max_height']       = 500;//указываем в px
            $config['file_ext_tolower']
                = true;//расширение файла в нижнем регистре
            $config['remove_spaces']
                = true;//меняем пробелы в имени на нижнее подчеркивание
            $config['encrypt_name']
                = true;//хешируем имя файлы, чтоб не переписать чужой логотип

            $this->load->library('upload', $config);

            //Если успешно загружен файл, пишем данные в БД
            if ($this->upload->do_upload('avatar_company')) {
                $this->load->model('company_cabinet/Company_model', 'CModel');

                $old_data
                    = $this->CModel->find_company_detail_by_id($_SESSION['ses_company_data']['company_id']);

                unlink('./uploads/companys_logo/' . $old_data['logo']);

                $new_logo = [
                    'logo' => $this->upload->data('file_name'),
                ];
                $resize = [
                    'image_library' => 'gd2',
                    'width' => 250,
                    'height' => 250,
                    'maintain_ratio' => true,
                    'source_image' => './uploads/companys_logo/' . $new_logo,
                    'quality' => 50,
                    'new_image' => './uploads/companys_logo/' . $new_logo,
                ];
                $this->load->library('image_lib', $resize);
                $this->image_lib->resize();
                $result
                    = $this->CModel->update_company_data($_SESSION['ses_company_data']['company_id'],
                    $new_logo);

                if ($result) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'success',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Логотип успешно загружен, сейчас эта страница обновится!',
                        ]));
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Увы, но нам не удалось сохранить новые данные!',
                        ]));
                }
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Увы, но нам не удалось загрузить логотип!',
                    ]));
            }
        }
    }

    public function getcredit()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $result = $this->CModel->store_credit(
            $_SESSION['ses_company_data']['company_id'],
            $this->input->post('sum_credit')
        );

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Заявка отправлена, скоро с Вами свяжется менеджер для уточнения деталей.',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка!',
                ]));
        }
    }

    public function change_fave()
    {
        if (empty($_POST)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'code' => -10,
                ]));
        }
        $this->load->model('company_cabinet/Company_model', 'CModel');

        $res = $this->CModel->change_fave($_SESSION['ses_company_data']['company_id'], $this->input->post('add_to_fave_id'));

        if ($res == -1) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'code' => $res,
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'code' => $res,
                ]));
        }
    }

    public function addfave()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $res
            = $this->CModel->add_fave($_SESSION['ses_company_data']['company_id'],
            $this->input->post('add_to_fave_id'));

        if ($res) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Компания добавлена в избранное!',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Это компания уже в избранных!',
                ]));
        }
    }

    public function deletefave()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $res
            = $this->CModel->delete_from_fave($_SESSION['ses_company_data']['company_id'],
            $this->input->post('fave_id'));

        if ($res) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Компания успешно удалена из избранного',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка!',
                ]));
        }
    }

    public function setcity()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('all_areas/Cities_model', 'CModel');
        $result
            = $this->CModel->setCity($_SESSION['ses_company_data']['company_id'],
            $this->input->post('q'));

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'fail',
                'csrf_token' => $this->security->get_csrf_hash(),
            ]));
    }

    public function review()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $from = $_SESSION['ses_company_data']['company_id'];
        $to = $this->input->post('to');
        $text = $this->input->post('text');

        $company_data = $this->CModel->find_company_detail_by_id($to);
        if($company_data['status'] == 0)
        {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Отправлено!",
                ]));
        }

        $data = [
            'status' => 0,
            'ban_comment' => "Ваша компания заблокирована по жалобе клиента.  
            Barter-Business заботится о том, чтобы все компании в каталоге четко
             соблюдали правила системы. Если вы хотите продолжить полноценную работу в системе перезвоните пожалуйста в Отдел Безопасности и Сервиса " . "<a href='tel:+79677398359'>7 967 739 83 59</a>",
        ];
        $this->CModel->change_data_company($data, $to);

        $result = $this->CModel->store_review($from, $to, $text);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Отзыв отправлен!",
                    'status' => 'success',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "fail",
                ]));
        }
    }

    public function review_comp()
    {
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
        $from = $_SESSION['ses_company_data']['company_id'];
        $to = $this->input->post('to');
        $text = $this->input->post('text');
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $result = $this->CModel->store_review_comp($from, $to, $text);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Отзыв отправлен!",
                    'status' => 'success',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "fail",
                ]));
        }
    }

    public function deleteproduct()
    {
        $id = $_SESSION['ses_company_data']['company_id'];
        $product = $this->input->post('product_id');
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $result = $this->CModel->delete_product($id, $product);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'status' => 'success',
                    'text_message' => 'Страница сейчас обновится...',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Ошибка удаления!",
                    'status' => 'fail',
                ]));
        }
    }

    public function showmore()
    {
        $company = $this->input->post('company_id');
        $offset = $this->input->post('limit');
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $result = $this->CModel->get_products($company, 8, $offset);
        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'status' => 'success',
                    'data' => $result['products'],
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Ошибка удаления",
                    'status' => 'fail',
                ]));
        }
    }

    public function starrate()
    {
        $from = $_SESSION['ses_company_data']['company_id'];
        $to = $this->input->post('company_id');
        $rate = $this->input->post('rate');
        if ((float)$rate > 0 && (float)$rate < 6) {
            $this->load->model('company_cabinet/Company_model', 'CModel');
            $result = $this->CModel->rate_star($from, $to, $rate);
            if ($result) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'text_message' => "Ошибка",
                    'status' => 'fail',
                ]));
        }
    }

    public function getCashback()
    {
        $company = $_SESSION['ses_company_data']['deals_id'];

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $res = $this->CModel->getCashback($company, [
            'card_num' => $this->input->post('card_num'),
            'bank_holder' => $this->input->post('bank_holder'),
        ]);
        if ($res) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'text_message' => 'Спасибо! Ваша заявка отправлена. Страница сейчас перезагрузится..',
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'text_message' => 'Ошибка',
                    'status' => 'fail',
                ]));
        }
    }

    public function mutually_cancel()
    {
        $deal_id = $this->input->post('deal_id');
        $this->load->model('all_areas/Deals_model', 'DModel');

        $res = $this->DModel->mutuallyCancelDeal($deal_id);
        if ($res['status']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => $res['message'],
                    'status' => 'success',
                ]));

            return;
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => $res['message'],
                'status' => 'fail',
            ]));
    }

    public function update_about()
    {
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $descr = $this->input->post('about_company');
        $company_id = $_SESSION['ses_company_data']['company_id'];
        $res = $this->CModel->update_description_request($company_id, $descr);
        if ($res) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ваш запрос отправлен! Страница сейчас перезагрузится',
                    'status' => 'success',
                ]));

            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => 'Что-то пошло не так',
                'status' => 'fail',
            ]));
    }

    public function active_ref_balance()
    {
        $this->load->model('all_areas/Referral_model', 'RModel');
        $company_referral = $this->input->post('company_id');
        $company_owner = $_SESSION['ses_company_data']['company_id'];
        $type = $this->input->post('type');

        if ($type == 1 || $type == 2 || $type == 3) {
            //$res = $this->RModel->payout($company_owner, $company_referral);
            $res = $this->RModel->activeRefBalance($company_owner, $company_referral, $type);
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! Неверный вызов функции!',
                    'status' => 'fail',
                ]));
            return;
        }
        $msg = $res['message'];

        if ($res['result']) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => "Успешно! $msg",
                    'status' => 'success',
                ]));
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => $msg,
                    'status' => 'fail',
                ]));
        }
    }

    public function active_taxi_Comp()
    {
        $data = $this->input->post('data');

        $res = \Barter\Taxi::update($_SESSION['ses_company_data']['company_id'], $data);

        if ($res) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ваш запрос отправлен! Страница сейчас перезагрузится',
                    'status' => 'success',
                ]));

            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => 'Что-то пошло не так',
                'status' => 'fail',
            ]));
    }

    public function create_payment()
    {
        //--------------Убрать для активации сбера
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => 'Ошибка выполнения запроса!',
                'status' => 'fail',
            ]));
        return;
        //--------------Убрать для активации сбера

        $company_id = $_SESSION['ses_company_data']['company_id'];

        $this->load->model('company_cabinet/Payments_model', 'PModel');
        if (empty($this->input->post('type_payment'))) {
            if (empty($this->input->post('amount'))) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'text_message' => 'Ошибка выполнения запроса! Не введена сумма!',
                        'status' => 'fail',
                    ]));
            }
            $service = new SubscriptionSberbank(UPDATE_BALANCE, $company_id, $amount = $this->input->post('amount'), 'Пополнение бартерного баланса');
            //Создаем запись на оплату в бд и получаем ее номер
            $payment_id = $this->PModel->create_initial_payment($company_id);
            //Получаем ссылку на оплату
            $payment = $service->createPayment($payment_id);
        }
        else {
            $service = new SubscriptionSberbank($this->input->post('type_payment'), $company_id);
            //Создаем запись на оплату в бд и получаем ее номер
            $payment_id = $this->PModel->create_initial_payment($company_id);
            //Получаем ссылку на оплату
            $payment = $service->createPayment($payment_id);
        }

        if ($payment['status']) {
            $this->PModel->update_payment_data($payment_id, [
                'md_order' => $payment['orderId'],
                'summa' => $payment['amount']
            ]);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => $payment['formUrl'],
                    'status' => 'success',
                ]));
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка создания заказа! (err:' . $payment['errorCode'] . ')',
                    'status' => 'fail',
                ]));
        }

    }

    public function create_Ypayment()
    {
        $amount = $this->input->post('amount');
        $company_id = $_SESSION['ses_company_data']['company_id'];
        if (!empty($this->input->post('type_payment'))) {
            $service = new Subscription($this->input->post('type_payment'), $company_id);
            $payment = $service->createPayment();
        } else {
            $payment = Payment::getInstance()->createPayment(
                $amount,
                UPDATE_BALANCE,
                sprintf('Пополнение счета: ID %s', $company_id),
                ['company_id' => $company_id]);

        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => $payment->getConfirmation()->confirmationUrl,
                'status' => 'success',
            ]));
    }

    public function update_who_invite_company()
    {
        $company_id = (int)$this->input->post('company_id');
        $new_who_invite_company = (int)$this->input->post('id_who_invite_company');

        $data = [
            'id_who_invite_company' => $new_who_invite_company,
        ];

        $result = $this->CModel->change_data_company($data, $company_id);

        if ($result) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Все данные успешно обновлены, сейчас страница обновится!',
                ]));

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Увы, но нам не удалось сохранить новые данные!',
                ]));

        }

    }

    public function payout()
    {
        $company_id = (int)$this->input->post('company_id');

        $data = [
            'id_who_invite_company' => $new_who_invite_company,
        ];

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $result = $this->CModel->change_data_company($data, $company_id);

        if ($result) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Все данные успешно обновлены, сейчас страница обновится!',
                ]));

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Увы, но нам не удалось сохранить новые данные!',
                ]));

        }

    }

    public function update_credit()
    {
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $this->load->model('all_areas/Deals_model', 'DModel');

        $company_id = (int)$_SESSION['ses_company_data']['company_id'];
        $credit = abs($this->input->post('credit'));

        $company_detail = $this->CModel->find_company_detail_by_id($company_id);

        try
        {
            if ($company_detail['barter_balance'] < $credit) {
                throw new Exception('Попытка списать больше бартерного баланса!');
            }

            if ($company_detail['credit_balance'] < $credit) {
                throw new Exception('Вы пытаетесь списать больше выданного кредита!');
            }

            $data = [
                'credit_balance' => $company_detail['credit_balance'] - $credit,
                'barter_balance' => $company_detail['barter_balance'] - $credit,
            ];

            //Создаем сделку на погашение кредита
            $insert_data = [
                'seller_deal_id' => ADMIN_DEAL_ID,
                'buyer_deal_id' => $_SESSION['ses_company_data']['deals_id'],
                'summa_sdelki' => $credit,
                'comment_deal' => 'Погашение компанией кредита на сумму ' . $credit / 100 . ' бр.',
                'status_deal' => 4,
            ];

            $this->DModel->create_new_deal($insert_data);
            $result = $this->CModel->change_data_company($data, $company_id);

            if (!$result) {
                throw new Exception('Погасить кредит не удалось! Попробуйте еще раз...');
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Кредит успешно погашен на сумму: ' . $credit / 100 . ' руб.',
                ]));
        }
        catch (Exception $e)
        {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! ' . $e->getMessage(),
                ]));
            return;
        }
    }

    public function check_sub_status()
    {

        $company_id = $_SESSION['ses_company_data']['company_id'];

        $this->load->model('company_cabinet/Company_model', 'CModel');
        $result = $this->CModel->check_sub_status($company_id);

        if ($result < 0) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка проверки статуса оплаты АП!',
                ]));
        } else {

            if ($result == 1) {
                $msg = 'АП оплачена.';
            } else {
                $msg = 'АП не оплачена!';
            }
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'sub_status' => $result,
                    'text_message' => $msg,
                ]));
        }
    }

    public function get_available_coupons() {

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $result = $this->CModel->get_company_coupons((int) $_SESSION['ses_company_data']['company_id'], 0);

        if ($result) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'coupons' => $result,
                    'text_message' => 'Купоны успешно получены!',
                ]));

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Нет доступных купонов!',
                ]));

        }
    }

    public function select_ref_mode()
    {
        $this->load->model('company_cabinet/Company_model', 'CModel');

        if(!empty($this->input->post('ref_mode'))) {

            $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);
            $ref_mode = $this->input->post('ref_mode');

            if ($company_data['ref_mode'] == 0 && ($ref_mode == 1 || $ref_mode == 2)) {

                if($this->CModel->change_data_company(['ref_mode' => $ref_mode], $_SESSION['ses_company_data']['company_id'])) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'status' => 'success',
                        ]));
                    return;
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'status' => 'fail',
            ]));
    }

    public function get_ref_bonuses() {

        $this->load->model('publics/Company_model', 'CModel');
        $this->load->model('all_areas/Referral_model', 'RModel');

        $company_data = $this->CModel->find_company_and_manager($_SESSION['ses_company_data']['deals_id']);
        if ($company_data['sub_status'] == 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! АП не оплачена!',
                    'status' => 'fail',
                ]));
            return;
        }

        $total_withdrawal = $this->RModel->getTotalWidthdrawal($_SESSION['ses_company_data']['company_id']);
        $all_refs = $this->RModel->getReferrals_levels($_SESSION['ses_company_data']['company_id'], 2);
        //Сумма всех бонусов
        $total_sum = 0;
        foreach ($all_refs as $ref) {
            switch ($ref['level']) {
                case 0:
                    $total_sum += (int)($ref['deals_sum'] * REF_PERCENT_LV0);
                    break;
                case 1:
                    $total_sum += (int)($ref['deals_sum'] * REF_PERCENT_LV1);
                    break;
                case 2:
                    $total_sum += (int)($ref['deals_sum'] * REF_PERCENT_LV2);
                    break;
            }
        }
        $available_withdrawal = $total_sum - $total_withdrawal;
        if ($available_withdrawal > 0) {


            $deal_id = $this->CModel->createDealFromAdmin($_SESSION['ses_company_data']['company_id'], (int)$available_withdrawal, 'Бонус за уровневую реферальную программу');
            $this->RModel->createWidthdrawal($_SESSION['ses_company_data']['company_id'], $deal_id, (int)$available_withdrawal);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Успешно! Проверьте входящие сделки!',
                    'status' => 'success',
                ]));
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! Нет доступного баланса для вывода!',
                    'status' => 'fail',
                ]));
        }
    }

    public function load_company_stat() {

        $this->load->model('company_cabinet/Company_model', 'CModel');

        if (empty($this->input->post('date_start')) || empty($this->input->post('date_end'))) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! Не указана(некорректна) дата!',
                    'status' => 'fail',
                ]));
            return;
        }

        $date_start = new DateTime($this->input->post('date_start'));
        $date_end = new DateTime($this->input->post('date_end'));


        if ($date_start > $date_end) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка! Начальная дата не может быть больше конечной!',
                    'status' => 'fail',
                ]));
            return;
        }

        $stat = $this->CModel->manual_statistics($_SESSION['ses_company_data']['deals_id'], $date_start, $date_end);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => 'Данные успешно получены!',
                'data' => $stat,
                'start' => (new DateTime($this->input->post('date_start')))->format('d.m.Y'),
                'end' => (new DateTime($this->input->post('date_end')))->format('d.m.Y'),
                'status' => 'success'
            ]));
    }

    public function recommended_companies_ajax() {

        $this->load->model('company_cabinet/Company_model', 'CModel');

        $recommended_companies = $this->CModel->recommended_company_ext($_SESSION['ses_company_data']['company_id']);
        $recommended_companies_categories = $this->CModel->companies_categories(array_column($recommended_companies, 'company_id'));

        if(!empty($recommended_companies)) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Данные успешно получены!',
                    'data' => [
                        'comps' => $recommended_companies,
                        'cats' => $recommended_companies_categories
                    ],
                    'status' => 'success'
                ]));
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Ошибка загрузки рекомендуемых компаний!',
                    'status' => 'fail'
                ]));
        }

    }

    //Функция закрытия всех остальных сессий
    public function close_other_sessions() {

        $result = $this->close_sessions();

        if ($result >= 0) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'status' => 'success',
                    'text_message' => "Вы успешно вышли со всех остальных устройств!",
                ]));
        }
        else {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'status' => 'fail',
                    'text_message' => "Ошибка выполнения операции!",
                ]));
        }
    }

    private function close_sessions() {
        $this->load->model('company_cabinet/Company_model', 'CModel');
        //Сохраняем текущую сессию
        $current_ses = $_SESSION;
        //Получаем все сессии
        $sessions = $this->CModel->getsessions();
        //Массив с ИД сессий для закрытия
        $foreign_sessions = [];

        foreach ($sessions as $ses) {
            //Расшифровываем данные в blob
            foreach ($_SESSION as $key => $value){
                unset($_SESSION[$key]);
            }
            session_decode($ses['data']);
            $ses_data = $_SESSION;

            if (isset($ses_data['ses_company_data'])) {
                //Значит, это работник
                if ($ses_data['ses_company_data']['deals_id'] === $current_ses['ses_company_data']['deals_id'] &&
                    (int)$ses_data['__ci_last_regenerate'] !== (int)$current_ses['__ci_last_regenerate']) {
                    //Если его ИД совпадает с ИД в сессии и не совпадает время регенерации, то скорее всего это не текущая сессия, можем удалять
                    $foreign_sessions[] = $ses['id'];
                }
            }
        }

        //Возвращаем текущие данные сессии
        foreach ($_SESSION as $key => $value){
            unset($_SESSION[$key]);
        }
        $_SESSION = $current_ses;

        //Закрываем все вышенайденные сессии
        return $this->CModel->closesessions($foreign_sessions);
    }
}
