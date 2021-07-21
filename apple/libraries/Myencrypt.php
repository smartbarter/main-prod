<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Myencrypt {

    function password_encrypt($pass)
    {
    	$hash_format = "$2y$11$"; //говорим PHP что будем юзать blowfish и сообщаем стоимость
    	$salt_length = 22; //сообщаем длину соли
    	
    	// Not 100% unique, not 100% random, but good enough for a salt
		// MD5 returns 32 characters
		$unique_random_string = md5(uniqid(mt_rand(), true));
		  
		// Valid characters for a salt are [a-zA-Z0-9./]
		$base64_string = base64_encode($unique_random_string);
		  
		// But not '+' which is valid in base64 encoding
		$modified_base64_string = str_replace('+', '.', $base64_string);
		  
		// Truncate string to the correct length_salt
		$salt = substr($modified_base64_string, 0, $salt_length);


    	$format_and_salt = $hash_format . $salt;
    	$hash = crypt($pass, $format_and_salt);
    	return $hash;
    }

    function password_check($password, $existing_hash)
    {
		// Сравниваем хеши - введеный юзером и который есть у нас в БД
		$hash = crypt($password, $existing_hash);
		if ($hash === $existing_hash)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}