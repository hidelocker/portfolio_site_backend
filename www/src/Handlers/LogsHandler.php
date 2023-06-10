<?php

use App\Config\ResponseHttp;
use App\Controllers\LogsController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$request_uri = $_SERVER['REQUEST_URI'];
$headers = getallheaders();
$input_data = json_decode(file_get_contents('php://input'), true);
$get_data = $_GET;
$post_data = $_POST;

$app = new LogsController($method, $headers, $input_data, $post_data, $get_data);
$path_file = dirname(__DIR__) . '/Logs/' . NAME_LOG_FILE;

if ($method == 'post' && $request_uri == ENDPOINT_LOGS) $app->post($path_file);
else if ($method == 'get' && $request_uri == ENDPOINT_LOGS) $app->get($path_file);
else{

    echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
    exit;
}

