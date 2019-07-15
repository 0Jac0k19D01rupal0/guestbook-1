<?php

namespace App\Helper;


class Captcha
{
    private $url = 'https://www.google.com/recaptcha/api/siteverify';
    private $key = '6LdSnK0UAAAAAHuFmWnnciYiN923rAjaDPiJhKCs';

    public function request($response) {
        $query = $this->url.'?secret='.$this->key.'&response='.$response;
        $data = json_decode(file_get_contents($query));
        return $data;
    }
}