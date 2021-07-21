<?php

use Barter\CashActions;
use Barter\RegisterActions;

defined('BASEPATH') OR exit('No direct script access allowed');

//цепляем Telegram, т.к. тут отправка кода в чат идет
// use Telegram\Bot\Api;

class Registr_and_login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //заркываем доступ к методам, если нет POST запроса
        if (empty($_POST)) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
    }

    public function check_phone()
    {

        $this->load->model('publics/Company_model', 'CModel');
        $this->load->library('form_validation');

        $this->form_validation->set_rules(
            'company_phone',
            'Телефон',
            'trim|min_length[3]|max_length[16]|numeric'
        );

        if ($this->form_validation->run() == false) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'company_phone' => form_error('company_phone'),
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {

            //делаем запрос в БД, ищем номер телефона
            $result
                = $this->CModel->find_company_by_phone($this->input->post('company_phone'));

            if ($result) {
                //если TRUE, значит номер сущестует в БД
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'company_phone' => 'Такой телефон уже зарегистрирован!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));
            } else {

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));

            }

        }

    }

    public function registration()
    {

        $this->load->model('publics/Company_model', 'CModel');

        $this->load->model('admin_cabinet/Admin_model', 'AModel');
        $this->load->library('form_validation');//загружаем библиотеку валидации формы

        if ($this->form_validation->run('registration_company') == false) {
            //если не прошли валидацию, отправляем ошибки юзеру

            $array_errors = [
                'company_name' => form_error('company_name'),
                'company_phone' => form_error('company_phone'),
                'company_adress' => form_error('company_adress'),
                'company_contact_name' => form_error('company_contact_name'),
                'company_description' => form_error('company_description'),
                'barter_limit' => form_error('barter_limit'),
                'who_invited_company' => form_error('who_invited_company'),
                'accept_rules' => form_error('accept_rules'),
            ];

            //если у нас не заполнено скрытое поле, тогда показываем ошибку
            if (form_error('name_city_company')) {
                $array_errors += ['company_city' => form_error('name_city_company')];
            } else {
                $array_errors += ['company_city' => form_error('company_city')];
            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {
            //если валидацию прошли, принимаем данные и ищем номер телефона в БД, перед тем, как делать что-либо дальше

            //Подгружаем нашу библиотеку шифрования
            $this->load->library('myencrypt');
            $this->load->helper('string');
            $company_password = random_string('alnum', 6);
            $who_invited_company = $this->input->post('who_invited_company');

            $city_data = [
                'zip_code' => $this->input->post('zip_city_company'),
                'city_name' => $this->input->post('name_city_company'),
                'city_kladr_id' => $this->input->post('id_city_company_kladr'),
            ];

            $company_name = $this->input->post('company_name');
            $company_phone = $this->input->post('company_phone');
//            $company_city = $this->input->post('name_city_company');
            $company_adress = $this->input->post('company_adress');

            $add_text_in_description = "\n\n «" . $company_name . "» +"
                . $company_phone . " " . $company_adress . "";

            $company_data = [
                'for_deals_id' => mb_substr(md5(mt_rand() . time()), 0, 15,
                    'UTF-8'),
                'company_name' => $company_name,
                'contact_name' => $this->input->post('company_contact_name'),
                'company_phone' => $company_phone,
                'adress' => $this->input->post('company_adress'),
                'password' => $this->myencrypt->password_encrypt($company_password),
                //шифруем пароль
                'city_code' => $city_data['city_kladr_id'],
                'city_name' => $this->input->post('name_city_company'),
                'description_company' => $this->input->post('company_description')
                    . $add_text_in_description,
                'company_site' => '',
                'month_limit' => $this->input->post('barter_limit') + 300000,
                //хранится в копейках
                'dostupno_dlya_sdelok' => $this->input->post('barter_limit')
                    * 100,
                //хранится в копейках
            ];

            if (! empty($_SESSION['ses_worker_data']['deals_id'])) {
                $worker_data = $this->AModel->find_worker_by_id($_SESSION['ses_worker_data']['worker_id']);
                switch ($worker_data['status']) {
                    case AGENT_ID:
                        $company_data += [
                            'agent_id' => $_SESSION['ses_worker_data']['worker_id'],
                        ];
                        break;
                    default:
                        $company_data += [
                            'manager_id' => $_SESSION['ses_worker_data']['deals_id'],
                        ];
                        break;
                }
            }
            //ищем номер телефона в БД, т.к. он уникален, чтобы приложение не падало
            $find_company_by_phone = $this->CModel->find_company_by_phone($company_data['company_phone']);

            //если мы нашли при регистрации номер телефона в БД, говорим юзеру, что такой номер существует
            if ($find_company_by_phone) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'company_phone' => 'Такой телефон уже зарегистрирован!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));
            } else {
                //иначе, мы понимаем, что номера в БД нет, все отлично! Можем регать компанию
                //Но перед этим проверяем, а прикреплен ли логотип?
                if (! empty($_FILES)) {
                    //если файл прикреплен, подготавливаем конфиг и грузим файл
                    $config['upload_path']
                        = './uploads/companys_logo/';//куда грузим
                    $config['allowed_types']
                        = 'gif|jpg|jpeg|png';//допустимые форматы
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
                    if ($this->upload->do_upload("avatar_company")) {
                        //когда файл загружен, можем получить его имя и добавить в массив
                        $company_data += [
                            'logo' => $this->upload->data('file_name'),
                            //хэшированное имя файла с расширением
                        ];

                        //Пишем данные в БД
                        $result = $this->CModel->reg_company($city_data,
                            $company_data, $who_invited_company);

                        if ($result) {
                            //если успешная регистрация, добавляем данные компании в сессию
                            $this->session->set_userdata(
                                'ses_company_data', [
                                    'company_id' => $result,
                                    //сюда прилетает id компании
                                    'deals_id' => $company_data['for_deals_id'],
                                ]
                            );
                            $this->CModel->createCouponForRegister($result);
                            //здесь отправляем сообщение в ВК, манагерам
                            $this->load->library('vkbot');
                            //нужно найти манагеров, которые относяться к городу компании, чтобы им отправить уведомление
                            $find_managers
                                = $this->CModel->find_managers_in_company_city($city_data['city_kladr_id']);

                            if ($find_managers) {

                                $vk_chat_ids = [];
                                $array_length = count($find_managers);

                                for ($i = 0; $i < $array_length; $i++) {
                                    $vk_chat_ids += [$i => $find_managers[$i]['vk_chat_id']];
                                }

                                $ids = implode(',', $vk_chat_ids);

                                $this->vkbot->send_text_message('Зарегистрирована новая компания в вашем городе! Зайдите в личный кабинет и проверьте ее данные!',
                                    $ids);

                            }
                            $this->CModel->send_pass($company_phone,
                                $company_password);

                            //Обновляем геокод компании
                            $result2 = $this->CModel->update_company_geocode($_SESSION['ses_company_data']['company_id']) ? "Ok" : "Fail";

                            $this->output
                                ->set_content_type('application/json')
                                ->set_output(json_encode([
                                    'status' => 'success',
                                    'csrf_token' => $this->security->get_csrf_hash(),
                                    'text_message' => "Успешная регистрация! Сейчас вы будете перенаправлены в личный кабинет!",
                                ]));

                        } else {
                            //если зарегать чела не удалось - удаляем загруженный файл!

                            unlink('./uploads/companys_logo/'
                                . $company_data['logo']);

                            $this->output
                                ->set_content_type('application/json')
                                ->set_output(json_encode([
                                    'status' => 'fail',
                                    'response_data' => [
                                        'error_registr_fail' => 'К сожалению что-то пошло не так, попробуйте еще раз!',
                                    ],
                                    'csrf_token' => $this->security->get_csrf_hash(),
                                ]));

                        }//else


                    } else {
                        //если файл не удалось загрузить, говорим об этом юзеру

                        $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode([
                                'status' => 'fail',
                                'response_data' => [
                                    'avatar_company' => $this->upload->display_errors(),
                                ],
                                'csrf_token' => $this->security->get_csrf_hash(),
                            ]));

                    }//else

                }//if(!empty($_FILES))

            }//else

        }//else - валидация полей формы

    }

    public function regstr()
    {
        $this->load->model('publics/Company_model', 'CModel');
        $city_data = [
            'zip_code' => $this->input->post('zip_city_company'),
            'city_name' => $this->input->post('name_city_company'),
            'city_kladr_id' => $this->input->post('id_city_company_kladr'),
        ];

        $this->load->library('myencrypt');
        $this->load->helper('string');
        $company_password = random_string('alnum', 6);
        $company_name = $this->input->post('company_name');
        $company_phone = $this->input->post('company_phone');
        $company_data = [
            'for_deals_id' => mb_substr(md5(mt_rand() . time()), 0, 15,
                'UTF-8'),
            'company_name' => $company_name,
            'company_phone' => $company_phone,
            'password' => $this->myencrypt->password_encrypt($company_password),
            'adress' => '',
            'city_code' => $city_data['city_kladr_id'],
            'city_name' => $this->input->post('name_city_company'),
            'company_site' => '',
            'description_company' => 'null',
            'month_limit' => 30000000,
            'dostupno_dlya_sdelok' => 0,
        ];

        $find_company_by_phone
            = $this->CModel->find_company_by_phone($company_data['company_phone']);
        if ($find_company_by_phone) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'company_phone' => 'Такой телефон уже зарегистрирован!',
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

            return true;
        }
        if ($this->input->post('who_invite') !== null) {
            $who_invite = $this->CModel->get_phone_number_for_ref($this->input->post('who_invite'));
            if ($who_invite) {
                $result = $this->CModel->reg_company($city_data,
                    $company_data, $who_invite);
            } else {
                // check if its agent referal link
                $result = $this->CModel->reg_company($city_data,
                    $company_data);
                $this->load->model('admin_cabinet/Agent_model', 'AGModel');
                $this->AGModel->checkLinkAndUpdateCompany($result, $this->input->post('who_invite'));
            }
        } else {
            $result = $this->CModel->reg_company($city_data,
                $company_data);
        }

        if ($result) {
            //если успешная регистрация, добавляем данные компании в сессию
            $this->session->set_userdata(
                'ses_company_data', [
                    'company_id' => $result,
                    //сюда прилетает id компании
                    'deals_id' => $company_data['for_deals_id'],
                ]
            );
            $this->CModel->createCouponForRegister($result);
            // Проверить если входит в число первых 300 в своем городе
            $registerActions = new RegisterActions();
            if ($registerActions->isInFirstCity($result)) {
                $cash = new CashActions($result);
                $cash->addBarter(5000);
                $cash->addCreditBalance(5000);
            }
            //здесь отправляем сообщение в ВК, манагерам
            $this->load->library('vkbot');
            //нужно найти манагеров, которые относяться к городу компании, чтобы им отправить уведомление
            $find_managers
                = $this->CModel->find_managers_in_company_city($city_data['city_kladr_id']);

            if ($find_managers) {

                $vk_chat_ids = [];
                $array_length = count($find_managers);

                for ($i = 0; $i < $array_length; $i++) {
                    $vk_chat_ids += [$i => $find_managers[$i]['vk_chat_id']];
                }

                $ids = implode(',', $vk_chat_ids);

                $this->vkbot->send_text_message('Зарегистрирована новая компания в вашем городе! Зайдите в личный кабинет и проверьте ее данные!',
                    $ids);

            }
            $this->CModel->send_pass($company_phone,
                $company_password);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'text_message' => 'Успешная регистрация! Сейчас вы будете перенаправлены в личный кабинет!',
                ]));

            return true;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'fail',
                'csrf_token' => $this->security->get_csrf_hash(),
                'text_message' => 'Что-то пошло не так',
            ]));
    }

    public function login()
    {

        $this->load->model('publics/Company_model', 'CModel');

        $this->load->library('form_validation');//загружаем библиотеку валидации формы

        if ($this->form_validation->run('login_validation') == false) {

            $array_errors = [
                'company_login_phone' => form_error('company_login_phone'),
                'company_login_password' => form_error('company_login_password'),
            ];

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => $array_errors,
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {
            //если валидацию прошли
            //ищем номер телефона в БД

            $login_data = [
                'company_login_phone' => $this->input->post('company_login_phone'),
                'company_login_password' => $this->input->post('company_login_password'),
            ];

            $company_data
                = $this->CModel->find_company_by_phone($login_data['company_login_phone']);

            if ($company_data === false) {
                //если не нашли номер телефона в БД
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'message_login_fail' => 'Телефон или пароль не подходит!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));
            } else {
                //если номер найден, грузим библиотеку для пароля и сверяем пароль
                //Подгружаем нашу библиотеку шифрования
                $this->load->library('myencrypt');

                $check_pasw = $this->myencrypt->password_check(
                    $login_data['company_login_password'],
                    $company_data['password']
                );

                if ($check_pasw) {
                    //если хэши совпадают, тогда логиним юзера

                    $this->session->set_userdata(
                        'ses_company_data', [
                            'company_id' => $company_data['company_id'],
                            'deals_id' => $company_data['for_deals_id'],
                        ]
                    );

                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'success',
                            'csrf_token' => $this->security->get_csrf_hash(),
                            'text_message' => 'Успешная авторизация! Сейчас вы будете перенаправлены в личный кабинет!',
                        ]));

                } else {
                    //иначе выдаем ошибку, что что-то не то
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'response_data' => [
                                'message_login_fail' => 'Телефон или пароль не подходит!',
                            ],
                            'csrf_token' => $this->security->get_csrf_hash(),
                        ]));
                }
            }
        }
    }

    public function update()
    {
//        пример добавления сюда инфы
        /*
         * contact_name:
            company_address: Уфа
            company_limit:
            company_hobby:
            company_prices:
            company_hours:
            company_years:
            company_time:
            company_order_type:
        * Для доступа к полю: $this->input->post(<название>)
         */
        $data = [
            'adress' => $this->input->post('company_address'),
            'description_company' => '',
            'contact_name' => $this->input->post('contact_name'),
            'dostupno_dlya_sdelok' => $this->input->post('company_limit')
                * 100,
            'month_limit' => $this->input->post('company_limit') * 100,
        ];
        $this->load->model('company_cabinet/Company_model', 'CModel');
        $res = $this->CModel->update_company_data($data, $_SESSION['ses_company_data']['company_id']);

        if (! $res) {
            //если TRUE, значит номер сущестует в БД
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));
        } else {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'text_message' => "Успешная регистрация",
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        }

    }
}