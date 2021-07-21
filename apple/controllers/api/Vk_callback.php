<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vk_callback extends CI_Controller {
    
    public function index() {
        
        // $this->load->library('mylogs');
        $this->load->library('vkbot');
        
        //принимаем данные из запроса с ВК
        $data = json_decode(file_get_contents('php://input'));
        
        //если массив пустой, т.е. данных нет, отдаем ошибку
        if( !$data ) {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
        //если к нам пришли чужие данные и это не проверка, посылаем лесом!
        if($data->secret !== VK_SECRET_KEY && $data->type !== "confirmation") {
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }
        
        switch($data->type) {
            
            case "confirmation":
                    echo VK_RESPONSE_KEY;
                break;
                
            case "message_new":
                    
                    $user_id  = $data->object->user_id;
                    $msg_body = $data->object->body;
                    
                    $length_msg = strlen($msg_body);
                    
                    if($length_msg == 19) {
                        
                        //если длинна сообщения 15 символов, то скорей всего нам прислали код активации
                        
                        if(strpos($msg_body, 'comp') !== false) {
                            
                            $code = substr($msg_body, 4, 15);
                            $result = $this->find_confirm_bot_code($user_id, 'comp', $code);
                            
                        } elseif(strpos($msg_body, 'work') !== false) {
                            
                            $code = substr($msg_body, 4, 15);
                            $result = $this->find_confirm_bot_code($user_id, 'work', $code);
                            
                        }
                        
                        if($result) {
                             //если все отлично, отправляем сообщение юзеру
                            $this->vkbot->send_sticker(3065, $user_id);
                            echo('ok');
                        } else {
                            //просто показываем ВК сообщение, что скрипт отработал, ждать не надо.
                            // $text = 'Бот уже активирован! =)';
                            // $this->vkbot->send_text_message($text, $user_id);
                            echo('ok');
                        }
                        
                    }
                    
                break;

            default:
                echo('ok');
                break;
        }
        
    }
    
    private function find_confirm_bot_code($chat_id, $type_user, $code) {
        
        $this->load->model('publics/VK_bot_model', 'vkbm');
        
        $chek_active_bot = $this->vkbm->check_active_bot($type_user, $code);
        
        if($chek_active_bot) {
            
            $result = $this->vkbm->find_confirm_bot_code($code);
        
            if($result) {
                //если мы смогли найти код подтверждения
                //тогда мы пишем в БД ID юзера от ВК
                //а также тип юзера
                
                $data = [
                        'user_deals_id' => $code,
                        'vk_chat_id'    => $chat_id
                    ];
                    
                switch($type_user) {
                    case "comp":
                        
                        $data += ['user_type' => 'company'];
                        
                        break;
                    case "work":
                        
                        $data += ['user_type' => 'worker'];
                        
                        break;
                }
                
                $result_add = $this->vkbm->add_new_vk_chat_id($type_user, $code, $data);
                
                if($result_add) {
                    return TRUE;
                } else {
                    return FALSE;
                }
                
            } else {
                return FALSE;
            }
            
            
        } else {
            return FALSE;
        }
        
    }

    public function ping() {

        if(empty($_POST) || !isset($_POST['code'])) { 
            redirect(base_url() . 'publics/page_not_found', 'location', 301);
        }

        $this->load->model('publics/VK_bot_model', 'vkbm');

        $result = $this->vkbm->pong_detail(
                                    (string)$_POST['deals_id'],
                                    (int)$_POST['status'],
                                    (int)$_POST['bal']);

        if($result) {
            $result = 'OK';
        } else {
            $result = 'FAIL';
        }

        $post = [
            'result' => $result
        ];

        $this->getPOST($_POST['url'], $post);

    }

    private function getPOST($url, $post) {//CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.106 Safari/537.36');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
    }
    
}