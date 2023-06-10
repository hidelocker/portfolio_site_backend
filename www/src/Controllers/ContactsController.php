<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\ContactsModel;

class ContactsController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_num = '/^\d+$/';
    private static $validate_text = '/^[а-яА-ЯёЁa-zA-Z0-9?,.+_@=*:\-\/()^\s]+$/iu';

    public function __construct($method, $headers, $input_data, $post_data, $get_data)
    {

        $this->method = $method;
        $this->headers = $headers;
        $this->input_data = $input_data;
        $this->post_data = $post_data;
        $this->get_data = $get_data;
    }

    final public function get()
    {
            $model = new ContactsModel();
            echo json_encode($model->get());
            exit;
    }

    final public function put($id)
    {
            if (empty($this->input_data['name']) || empty($this->input_data['link'] || empty($id))) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (id, name, link)'));
            else if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
            else if (!preg_match(self::$validate_text, $this->input_data['name'])) echo json_encode(ResponseHttp::status400('Невалидное поле - name'));
            else if (!preg_match(self::$validate_text, $this->input_data['link'])) echo json_encode(ResponseHttp::status400('Невалидное поле - link'));
            else {

                $model = new ContactsModel();
                $model->setId($id);
                $model->setName($this->input_data['name']);
                $model->setLink($this->input_data['link']);

                echo json_encode($model->put());
            }

            exit;
    }
}
