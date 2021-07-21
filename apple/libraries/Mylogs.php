<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mylogs {

    public function write_log($message) {
        
        $log_file_name = './uploads/logs.txt';

        if(file_exists($log_file_name)) {
            $log = array_diff(explode("\r\n", file_get_contents($log_file_name)), array(''));
        }
    
        $log[] = date("m.d.Y-H:i:s").' | ' . $message;
    
        if(file_put_contents($log_file_name, implode("\r\n", $log))) {
            return true;
        } else {
            return false;
        }
        
    }

}