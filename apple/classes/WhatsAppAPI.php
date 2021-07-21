<?php

class WhatsAppAPI extends CI_Controller
{
    var $APIurl = 'https://api.chat-api.com/instance90812/';
    var $token = 'k7h40drotukkpjte';

    public function __construct(){
        parent::__construct();
    }
    public function sendMessage($phone, $text){

        $data = array('phone'=>$phone,'body'=>$text);
        return $this->sendRequest('sendMessage', $data)['sent'];
    }

    public function sendRequest($method,$data){

        $url = $this->APIurl.$method.'?token='.$this->token;
        if(is_array($data)){ $data = json_encode($data);}
        $options = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $data]]);
        $response = file_get_contents($url,false,$options);
        return json_decode($response);
        //file_put_contents('requests.log',$response.PHP_EOL,FILE_APPEND);
    }
}