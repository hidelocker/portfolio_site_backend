<?php

namespace App\Config;

class ResponseHttp
{

    public static $message = array(
        'status' => '',
        'body' => ''
    );

    final public static function cors(){

        echo header("Access-Control-Allow-Origin: *");
        echo header("Access-Control-Allow-Methods: GET,PUT,POST,PATCH,DELETE");
        echo header("Allow: GET,PUT,POST,PATCH,DELETE");
        echo header('Access-Control-Allow-Credentials: true');
        echo header("Access-Control-Allow-Headers: X-API-KEY, Origin, Cookie, X-Requested-With, Content-Type, Accept, Authorization");
        echo header("Content-Type: application/json");

        if($_SERVER['REQUEST_METHOD'] == 'OPTIONS') exit(0);
    }

    final public static function status200($message = 'Запрос успешно выполнен!')
    {

        http_response_code(200);
        self::$message['status'] = true;
        self::$message['body'] = $message;

        return self::$message;
    }

    final public static function status201($message = 'Запись успешно создана!')
    {

        http_response_code(201);
        self::$message['status'] = true;
        self::$message['body'] = $message;

        return self::$message;
    }

    final public static function status400($message = 'Некорректный запрос!')
    {

        http_response_code(400);
        self::$message['status'] = false;
        self::$message['body'] = $message;

        return self::$message;
    }

    final public static function status401($message = 'Доступ запрещен!')
    {

        http_response_code(401);
        self::$message['status'] = 'error';
        self::$message['body'] = $message;

        return self::$message;
    }

    final public static function status404($message = 'Не найдено!')
    {

        http_response_code(404);
        self::$message['status'] = false;
        self::$message['body'] = $message;

        return self::$message;
    }

    final public static function status500($message = 'Серверная ошибка!')
    {

        http_response_code(500);
        self::$message['status'] = false;
        self::$message['body'] = $message;

        return self::$message;
    }
}
