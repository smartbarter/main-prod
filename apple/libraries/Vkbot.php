<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Vkbot {

    public function send_text_message($text, $user_id) {
        
        $params = [
            'user_ids'      => $user_id,
            'message'       => $text,
            'access_token'  => VK_TOKEN,
            'v'             => '5.74'
        ];
        
        file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($params));
        // echo('ok');
    }
    
    public function send_sticker($sticker_id, $user_id) {
        $params = [
            'user_ids'     => $user_id,
            'sticker_id'   => $sticker_id,
            'access_token' => VK_TOKEN,
            'v'            => '5.74'
        ];
        
        file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($params));
        // echo('ok');
    }

}