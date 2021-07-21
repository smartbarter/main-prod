<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//класс для восстановления пароля
class Recover_password extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //заркываем доступ к методам, если нет POST запроса
        if (empty($_POST)) {
            redirect(base_url().'publics/page_not_found', 'location', 301);
        }

        $this->load->library('form_validation');
        $this->load->model('publics/Recover_pass_model', 'R_PAS_Model');
        $this->load->model('publics/VK_bot_model', 'VK_Model');

    }

    //Первый шаг восстановления пароля
    public function index()
    {

        //сюда приходит запрос с номером телефона, валидируем данные

        if ($this->form_validation->run('recover_pass_validation_step_one')
            == false
        ) {
            //если валидацию не прошли

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'recover_password_phone' => form_error('recover_password_phone'),
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {
            //если валидацию прошли
            //принимаем телефон и ищем его в нашей БД

            $phone = $this->input->post('recover_password_phone');

            $user_type = $this->input->post('type_user');

            switch ($user_type) {
                case "company":

                    $this->generate_recover_code_step_one($user_type, $phone);

                    break;
                case "manager_or_admin":

                    echo "должны работать с админом";

                    break;

                default:

                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'response_data' => [
                                'generate_code' => 'Сорян братан, но я тебя не знаю!',
                            ],
                            'csrf_token' => $this->security->get_csrf_hash(),
                        ]));

                    break;
            }

        }

    }

    //функция для восстановления пароля
    private function generate_recover_code_step_one($user_type, $phone)
    {

        if ($user_type == "company") {

            //грузим модель для компании
            $this->load->model('publics/Company_model', 'CModel');

            //ищем телефон в БД и здесь нам возвращаются все данные компании
            $user_data = $this->CModel->find_company_by_phone($phone);

        } elseif ($user_type == "manager_or_admin") {

            //если у нас манагер или админ восстанавливает пароль от аккаунта

        }

        if ($user_data === false) {
            //если ничего не нашли

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'generate_code' => 'Такого телефона не существует!',
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {
            //если совпадает генерим произвольную цифру из 6 символов
            $data_insert = [
                'phone' => $phone,
                'code' => rand(0, 99999),
                'time' => time() + 60 * 5
                //плюсуем к нынешнему времени 5 минут, т.к. код будет жить только 5 минут
            ];
            // Телефон тут уже существует
            // 1. Создать код и отправить по СМС

            $result = $this->R_PAS_Model->add_recovery_code($data_insert);
            if ($result) {
                $res = $this->CModel->send_sms_activation_code($phone,
                    $data_insert['code']);
                if ($res) {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'success',
                            'csrf_token' => $this->security->get_csrf_hash(),
                        ]));

                    return;
                } else {
                    $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'fail',
                            'response_data' => [
                                'generate_code' => 'Ошибка: не удалось отправить сообщение. Свяжитесь с менеджером!',
                            ],
                            'csrf_token' => $this->security->get_csrf_hash(),
                        ]));

                    return;
                }
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'generate_code' => 'Произошла ошибка. Свяжитесь с менеджером!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));

                return;
            }


            //Проверяем, есть ли у нас ID чата с юзером, который запрашивает восстановление
//            if ($user_data['active_bot'] != 0) {
//                //пишем цифру в БД, нам возвращается номер чата из БД, куда отправить код!
//                $result
//                    = $this->R_PAS_Model->add_recovery_code($user_data['for_deals_id'],
//                    $data_insert);
//
//                if ($result) {
//
//                    //если данные сохранены в БД и нам вернулся номер чата, отправляем сообщение
//                    $this->load->library('vkbot');
//                    $this->vkbot->send_text_message('Код восстановления пароля: '
//                        .$data_insert['code'], $result['vk_chat_id']);
//
//
//                    //отправляем с помощью бота юзеру в ВК
//                    $this->output
//                        ->set_content_type('application/json')
//                        ->set_output(json_encode([
//                            'status' => 'success',
//                            'csrf_token' => $this->security->get_csrf_hash(),
//                        ]));
//
//                } else {
//
//                    $this->output
//                        ->set_content_type('application/json')
//                        ->set_output(json_encode([
//                            'status' => 'fail',
//                            'response_data' => [
//                                'generate_code' => 'Похоже вы не активировали бота в Вконтакте или произошла ошибка! Свяжитесь с менеджером!',
//                            ],
//                            'csrf_token' => $this->security->get_csrf_hash(),
//                        ]));
//
//                }
//
//            } else {
//
//                $this->output
//                    ->set_content_type('application/json')
//                    ->set_output(json_encode([
//                        'status' => 'fail',
//                        'response_data' => [
//                            'generate_code' => 'Похоже вы не активировали бота в Вконтакте или произошла ошибка! Свяжитесь с менеджером!',
//                        ],
//                        'csrf_token' => $this->security->get_csrf_hash(),
//                    ]));
//
//            }

        }

    }

    //второй шаг восстановления пароля
    //проверяем код, который нам прислал юзер
    public function confirm_activation_code()
    {

        if ($this->form_validation->run('recover_pass_calidation_step_two')
            == false
        ) {
            //если не смогли пройти валидацию

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'activation_code' => form_error('activation_code'),
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {
            //если прошли валидацию
            $active_code = $this->input->post('activation_code');

            $result_active_code
                = $this->R_PAS_Model->find_active_code($active_code);

            if ($result_active_code) {

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));

            } else {

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'generate_code' => 'Похоже ваш код недействителен! Попробуйте запросить новый код восстановления!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));

            }

        }

    }

    public function update_password()
    {

        //валидация
        if ($this->form_validation->run('recover_pass_calidation_step_three')
            == false
        ) {

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'response_data' => [
                        'update_password' => form_error('update_password'),
                    ],
                    'csrf_token' => $this->security->get_csrf_hash(),
                ]));

        } else {

            $this->load->library('myencrypt');

            $user_type = $this->input->post('type_user');
            $user_phone = $this->input->post('user_pass_phone');
            $user_pass = $this->input->post('update_password');

            $new_user_pass = $this->myencrypt->password_encrypt($user_pass);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'fail',
                    'csrf_token' => $this->security->get_csrf_hash(),
                    'income_data' => $_POST,
                ]));

            $update_pass = $this->R_PAS_Model->update_password($user_type,
                $user_phone, $new_user_pass);

            if ($update_pass) {

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'success',
                        'csrf_token' => $this->security->get_csrf_hash(),
                        'success_message' => 'Пароль успешно сохранен!',
                    ]));

            } else {

                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 'fail',
                        'response_data' => [
                            'generate_code' => 'К сожалению нам не удалось обновить пароль! Попробуйте еще раз!',
                        ],
                        'csrf_token' => $this->security->get_csrf_hash(),
                    ]));

            }

        }
    }

}