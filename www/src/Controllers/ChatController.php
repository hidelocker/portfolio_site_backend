<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Orhanerday\OpenAi\OpenAi;
use OpenAI\Client;
use OpenAI\Model\Completion;
use OpenAI\Model\Engine;
use OpenAI\Request\CompletionRequest;

class ChatController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_num = '/^\d+$/';

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
            if (empty($this->input_data['content'])) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (content)'));
            else if (mb_strlen($this->input_data['content']) > 400) echo json_encode(ResponseHttp::status400('Максимальная длина сообщения - 400 символов'));
            else {


                $client = new OpenAi(Security::getChatGptKey());

                $chat = $client->chat([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            "role" => "system",
                            "content" => "Ты помощник сайта akella.su",
                        ],
                        [
                            "role" => "user",
                            "content" => $this->input_data['content'],
                        ],
                    ],
                    'temperature' => 1.0,
                    'max_tokens' => 4000,
                    'frequency_penalty' => 0,
                    'presence_penalty' => 0,
                ]);


                $res = json_decode($chat, true);

                if (!empty($res['choices'][0]['message']['content'])) echo json_encode(ResponseHttp::status200($res['choices'][0]['message']['content']));
                else if (!empty($res['error']['message'])) echo json_encode(ResponseHttp::status400($res['error']['message']));
                else {
                    error_log(json_encode($res));
                    echo json_encode(ResponseHttp::status500());
                }
            }

            exit;
        } catch (\Exception $e) {

            error_log("ChatController::post -> \n$e\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
