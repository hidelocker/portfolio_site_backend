<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;

class OrdersController
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

    final public function post()
    {
        try {
            if (empty($this->input_data['name']) || empty($this->input_data['telegram']) || empty($this->input_data['description']) || empty($this->input_data['element']) || empty($this->input_data['jabber'])) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (name, telegram, description, element, jabber, term)'));
            else if (!preg_match(self::$validate_text, $this->input_data['name'])) echo json_encode(ResponseHttp::status400('Невалидное поле - name'));
            else if (!preg_match(self::$validate_text, $this->input_data['telegram'])) echo json_encode(ResponseHttp::status400('Невалидное поле - telegram'));
            else if (!preg_match(self::$validate_text, $this->input_data['description'])) echo json_encode(ResponseHttp::status400('Невалидное поле - description'));
            else if (!preg_match(self::$validate_text, $this->input_data['element'])) echo json_encode(ResponseHttp::status400('Невалидное поле - element'));
            else if (!preg_match(self::$validate_text, $this->input_data['jabber'])) echo json_encode(ResponseHttp::status400('Невалидное поле - jabber'));
            else if (!preg_match(self::$validate_num, $this->input_data['term'])) echo json_encode(ResponseHttp::status400('Невалидное поле - term'));
            else if (mb_strlen($this->input_data['name']) > 1000) echo json_encode(ResponseHttp::status400('Максимальное значение поля name 50 символов'));
            else if (mb_strlen($this->input_data['telegram']) > 50) echo json_encode(ResponseHttp::status400('Максимальное значение поля telegram 50 символов'));
            else if (mb_strlen($this->input_data['description']) > 1000) echo json_encode(ResponseHttp::status400('Максимальное значение поля description 1000 символов'));
            else if (mb_strlen($this->input_data['element']) > 50) echo json_encode(ResponseHttp::status400('Максимальное значение поля element 50 символов'));
            else if (mb_strlen($this->input_data['jabber']) > 50) echo json_encode(ResponseHttp::status400('Максимальное значение поля jabber 50 символов'));
            else if (mb_strlen($this->input_data['term']) > 10) echo json_encode(ResponseHttp::status400('Максимальное значение поля term 10 символов'));
            else {

                $name = $this->input_data['name'];
                $telegram = $this->input_data['telegram'];
                $description = $this->input_data['description'];
                $element = $this->input_data['element'];
                $jabber = $this->input_data['jabber'];
                $term = !empty($this->input_data['term']) ? $this->input_data['term'] : 0;
                $ip = $_SERVER['REMOTE_ADDR'];

                $text = "<b>Имя|~</b>       $name\n
                         <b>Телеграм|~</b>  $telegram\n
                         <b>Описание|~</b>  $description\n
                         <b>Element|~</b>   $element\n
                         <b>Jabber|~</b>    $jabber\n
                         <b>Сроки|~</b>     $term\n
                         <b>IP|~</b>       $ip\n
                    ";

                $input_data = [
                    'chat_id' => Security::getTelegramChatId(),
                    'text' => str_replace(' ', '', $text),
                    'parse_mode' => 'HTML'
                ];

                $query = "https://api.telegram.org/bot" . Security::getTelegramBotKey() . "/sendMessage?" . http_build_query($input_data);
                $response = file_get_contents($query);
                $response = json_decode($response, 1);

                if (!$response['ok']) json_encode(ResponseHttp::status400());
                else {

                    echo json_encode(ResponseHttp::status200("Заказ успешно отправлен!"));
                }
            }
        } catch (\Exception $e) {

            error_log("OrdersController::post -> \n{$e}\n\n");
            die(json_encode(ResponseHttp::status500()));
        }

        exit;
    }
}
