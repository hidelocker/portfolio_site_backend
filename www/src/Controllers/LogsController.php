<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;

class LogsController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_text = '/^[а-яА-ЯёЁa-zA-Z0-9?,.+_@=*:\-\/()^\s]+$/iu';

    public function __construct($method, $headers, $input_data, $post_data, $get_data)
    {
        $this->method = $method;
        $this->headers = $headers;
        $this->input_data = $input_data;
        $this->post_data = $post_data;
        $this->get_data = $get_data;
    }

    final public function get($path_file)
    {
        try {

            $content = file_get_contents($path_file);
            $size = filesize($path_file);

            $data = array(

                'content' => $content,
                'size_bytes' => $size
            );

            echo json_encode(ResponseHttp::status200($data));
        } catch (\Exception $e) {

            error_log("LogController::get_log_content ->\n" . $e . "\n");
            die(ResponseHttp::status500('Не удалось прочитать файл! Ошибка сервера!'));
        }

        exit;
    }

    final public function post($path_file)
    {

        try {

            file_put_contents($path_file, "");
            echo json_encode(ResponseHttp::status200('Лог файл успешно очищен!'));
        } catch (\Exception $e) {

            error_log("LogController::clear_log ->\n" . $e . "\n");
            die(ResponseHttp::status500('Не удалось очистить файл! Ошибка сервера!'));
        }

        exit;
    }
}
