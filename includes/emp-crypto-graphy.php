<?php
Class SafeCrypto {
	public function encrypt($string){
        $output = false;
	    $encrypt_method = "AES-256-CBC";
	    //pls set your unique hashing key
	    $secret_key = 'muni';
	    $secret_iv = 'muni123';
	    // hash
	    $key = hash('sha256', $secret_key);
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
	    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        return base64_encode($output);
    }
        
    public function decrypt($string){
       	$output = false;
	    $encrypt_method = "AES-256-CBC";
	    //pls set your unique hashing key
	    $secret_key = 'muni';
	    $secret_iv = 'muni123';
	    // hash
	    $key = hash('sha256', $secret_key);
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr(hash('sha256', $secret_iv), 0, 16);
        return openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);;
    }
}

