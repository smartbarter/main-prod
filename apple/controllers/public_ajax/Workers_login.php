<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workers_login extends CI_Controller {

	public function __construct(){
	   parent::__construct();
		
		//заркываем доступ к методам, если нет POST запроса
		if(empty($_POST)) {
			redirect(base_url() . 'publics/page_not_found', 'location', 301);
		}

    }

    public function index() {
        //в функции имеются переменные и названия company <- это из-за того, что
        //функция взята из public_ajax/registr_and_login/login, чтобы не писать валидацию и т.д.

        $this->load->model('admin_cabinet/Admin_model','AModel');

        $this->load->library('form_validation');//загружаем библиотеку валидации формы

		if($this->form_validation->run('login_validation') == FALSE) {

			$array_errors = [
				'company_login_phone'    => form_error('company_login_phone'),
				'company_login_password' => form_error('company_login_password'),
			];

			$this->output
					->set_content_type('application/json')
					->set_output(json_encode(array(
						'status'        => 'fail',
						'response_data' => $array_errors,
						'csrf_token'    => $this->security->get_csrf_hash()
					)));

		} else {
			//если валидацию прошли
			//ищем номер телефона в БД

			$login_data = [
				'company_login_phone'    => $this->
											input->
											post('company_login_phone'),
				'company_login_password' => $this->
											input->
											post('company_login_password')
			];

			$company_data = $this->
							AModel->
							find_worker_by_phone($login_data['company_login_phone']);

			if($company_data === FALSE) {
				//если не нашли номер телефона в БД
				$this->output
						->set_content_type('application/json')
						->set_output(json_encode(array(
							'status'        => 'fail',
							'response_data' => array(
								'message_login_fail' => 'Телефон или пароль не подходит!'
							),
							'csrf_token'    => $this->security->get_csrf_hash()
						)));
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
								'ses_worker_data', array(
										'worker_id' => $company_data['user_id'],
										'deals_id'   => $company_data['for_deals_id']
									)
                    			);

                    //проверяем, а активировал ли юзер бота от ВК
                    if($company_data['active_bot'] == 1) {
                        //если бот активен, то шлем сообщение в ВК, что произведен вход в аккаунт

                        $this->load->library('vkbot');
                        $this->load->model('publics/VK_bot_model','VKModel');

                        $chat_id = $this->VKModel->find_vk_chat_id($company_data['for_deals_id']);

                       

                    }

					$this->output
								->set_content_type('application/json')
								->set_output(json_encode(array(
									'status'       => 'success',
									'csrf_token'   => $this->security->get_csrf_hash(),
									'text_message' => 'Сейчас вы будете перенаправлены в административную панель!'
								)));

				} else {
					//иначе выдаем ошибку, что что-то не то

					$this->output
							->set_content_type('application/json')
							->set_output(json_encode(array(
								'status'        => 'fail',
								'response_data' => array(
									'message_login_fail' => 'Телефон или пароль не подходит!'
								),
								'csrf_token'    => $this->security->get_csrf_hash()
							)));

				}

			}

		}
    }
}