<?php

use App\Config\ResponseHttp;
use App\Controllers\ListIpController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$request_uri = $_SERVER['REQUEST_URI'];
$headers = getallheaders();
$input_data = json_decode(file_get_contents('php://input'), true);
$get_data = $_GET;
$post_data = $_POST;

$app = new ListIpController($method, $headers, $input_data, $post_data, $get_data);

if (isset($id)) {

    if ($method == 'get' && $request_uri == ENDPOINT_LIST_IP . "/page/$id") $app->get($id);
    else if ($method == 'put' && $request_uri == ENDPOINT_LIST_IP . "/$id") $app->put($id);
    else if ($method == 'delete' && $request_uri == ENDPOINT_LIST_IP . "/$id") $app->delete($id);
    else {

        echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
        exit;
    }
}
else{

    echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
    exit;
}

