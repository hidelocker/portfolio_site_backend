<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\ListIpModel;

class ListIpController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_num = '/^\d+$/';
    private static $validate_text = '/^[а-яА-ЯёЁa-zA-Z0-9?,.+_@=*:\-\/()^\s]+$/iu';
    private static $validate_ip = '/(\b25[0-5]|\b2[0-4][0-9]|\b[01]?[0-9][0-9]?)(\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}/';

    public function __construct($method, $headers, $input_data, $post_data, $get_data)
    {

        $this->method = $method;
        $this->headers = $headers;
        $this->input_data = $input_data;
        $this->post_data = $post_data;
        $this->get_data = $get_data;
    }

    final public function get($page)
    {


        if (!preg_match(self::$validate_num, $page)) echo json_encode(ResponseHttp::status400('Невалидное поле - page'));
        else {

            $model = new ListIpModel();
            echo json_encode($model->get($page));
        }

        exit;
    }

    final public function put($id)
    {

        if (empty($this->input_data['ip']) || empty($this->input_data['status_blocking']) || empty($id)) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (id, ip, status_blocking)'));
        else if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
        else if (!preg_match(self::$validate_ip, $this->input_data['ip'])) echo json_encode(ResponseHttp::status400('Невалидное поле - ip'));
        else if (!preg_match(self::$validate_text, $this->input_data['status_blocking'])) echo json_encode(ResponseHttp::status400('Невалидное поле - status_blocking'));
        else {

            $model = new ListIpModel();
            $model->setId($id);
            $model->setIp($this->input_data['ip']);
            $model->setLastVisit(date('d.m.Y h:i:s', time()));
            $model->setStatusBlocking($this->input_data['status_blocking']);

            echo json_encode($model->put());
        }

        exit;
    }

    final public function delete($id)
    {

        if (empty($id)) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (id)'));
        else if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
        else {

            $model = new ListIpModel();
            $model->setId($id);

            echo json_encode($model->delete());
        }

        exit;
    }
}
