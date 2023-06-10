<?php

use App\Config\ResponseHttp;
use App\Controllers\OrdersController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$request_uri = $_SERVER['REQUEST_URI'];
$headers = getallheaders();
$input_data = json_decode(file_get_contents('php://input'), true);
$get_data = $_GET;
$post_data = $_POST;

$app = new OrdersController($method, $headers, $input_data, $post_data, $get_data);

if ($method == 'post' && $request_uri == ENDPOINT_ORDER) $app->post();
else {

    echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
    exit;
}
