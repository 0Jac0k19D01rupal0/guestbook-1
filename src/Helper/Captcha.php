<?php

namespace App\Helper;

class Captcha
{
    private $url = 'https://www.google.com/recaptcha/api/siteverify';

    public function request($key, $response) {
        $query = $this->url.'?secret='.$key.'&response='.$response;
        $data = json_decode(file_get_contents($query));
        return $data;
    }
}