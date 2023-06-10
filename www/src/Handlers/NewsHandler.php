<?php

use App\Config\ResponseHttp;
use App\Controllers\NewsController;

$method = strtolower($_SERVER['REQUEST_METHOD']);
$request_uri = $_SERVER['REQUEST_URI'];
$headers = getallheaders();
$input_data = json_decode(file_get_contents('php://input'), true);
$get_data = $_GET;
$post_data = $_POST;

$app = new NewsController($method, $headers, $input_data, $post_data, $get_data);

if ($method == 'post' && $request_uri == ENDPOINT_NEWS) $app->post();
else if (isset($id)) {

    if ($method == 'get' && $request_uri == ENDPOINT_NEWS . "/$id") $app->single($id);
    else if ($method == 'get' && $request_uri == ENDPOINT_NEWS . "/page/$id") $app->get($id);
    else if ($method == 'post' && $request_uri == ENDPOINT_NEWS . "/put/$id") $app->put($id);
    else if ($method == 'delete' && $request_uri == ENDPOINT_NEWS . "/$id") $app->delete($id);
    else {

        echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
        exit;
    }
} else {

    echo json_encode(ResponseHttp::status404('Не найден подходящий контроллер для данного запроса! Проверьте свой запрос!'));
    exit;
}
