<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\AdminModel;

class AdminController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_login = '/^[a-zA-Z0-9]+$/';
    private static $validate_pass = '/^[a-zA-Z0-9@_-]+$/';

    public function __construct($method, $headers, $input_data, $post_data, $get_data)
    {

        $this->method = $method;
        $this->headers = $headers;
        $this->input_data = $input_data;
        $this->post_data = $post_data;
        $this->get_data = $get_data;
    }

    final public function post()
    {
        if (empty($this->input_data['login']) || empty($this->input_data['pass'])) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (login, pass)'));
        else if (!preg_match(self::$validate_login, $this->input_data['login'])) echo json_encode(ResponseHttp::status400('Логин должен содержать только буквы и цифры'));
        else if (!preg_match(self::$validate_pass, $this->input_data['pass'])) echo json_encode(ResponseHttp::status400('Пароль должен содрежать только допустимые символы (@_-)'));
        else {

            $model = new AdminModel();
            $model->setLogin($this->input_data['login']);
            $model->setPass($this->input_data['pass']);

            echo json_encode($model->login());
        }

        exit;
    }

    final public function put()
    {
        if (empty($this->input_data['old_pass']) || empty($this->input_data['new_pass']) || empty($this->input_data['confirm_pass'])) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (old_pass, new_pass, confirm_pass)'));
        else if (!preg_match(self::$validate_pass, $this->input_data['new_pass'])) echo json_encode(ResponseHttp::status400('Новый пароль должен содрежать только допустимые символы (@_-)'));
        else if ($this->input_data['new_pass'] != $this->input_data['confirm_pass']) echo json_encode(ResponseHttp::status400('Пароли не совпадают'));
        else {

            $model = new AdminModel();
            $jwt_data = Security::getTokenJwtData();

            if (!$model->validatePassword($jwt_data['id_token'], $this->input_data['old_pass'])) echo json_encode(ResponseHttp::status400('Неправильный старый пароль'));
            else {

                $model->setPass($this->input_data['new_pass']);
                $model->setIdToken($jwt_data['id_token']);
                $model->setLastUpdated(date('d.m.Y h:i:s', time()));

                echo json_encode($model->put());
            }
        }

        exit;
    }
}
