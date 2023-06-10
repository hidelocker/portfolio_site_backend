<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/router.php';

use App\Config\ErrorLog;
use App\Config\ResponseHttp;
use App\Config\Security;

ResponseHttp::cors();
ErrorLog::activateErrorLog();
Security::check_banned();
try {

    //Публичные обработчики
    get(ENDPOINT_NEWS           . '/$id',               PATH_HANDLERS_FOLDER . 'NewsHandler.php');
    get(ENDPOINT_NEWS           . '/page/$id',          PATH_HANDLERS_FOLDER . 'NewsHandler.php');
    get(ENDPOINT_PROJECTS       . '/page/$id',          PATH_HANDLERS_FOLDER . 'ProjectsHandler.php');
    get(ENDPOINT_CONTACTS,                              PATH_HANDLERS_FOLDER . 'ContactsHandler.php');
    get(ENDPOINT_CATEGORIES,                            PATH_HANDLERS_FOLDER . 'CategoriesHandler.php');
    post(ENDPOINT_CHAT,                                 PATH_HANDLERS_FOLDER . 'ChatHandler.php');
    post(ENDPOINT_ORDER,                                PATH_HANDLERS_FOLDER . 'OrderHandler.php');
    post(ENDPOINT_ADMIN_LOGIN,                          PATH_HANDLERS_FOLDER . 'AdminHandler.php');

    //Приватные обработчики
    Security::validateTokenJwt(Security::getSecretKey(), getallheaders());
    get(ENDPOINT_LIST_IP        . '/page/$id',          PATH_HANDLERS_FOLDER . 'ListIpHandler.php');
    get(ENDPOINT_LOGS,                                  PATH_HANDLERS_FOLDER . 'LogsHandler.php');

    post(ENDPOINT_NEWS,                                 PATH_HANDLERS_FOLDER . 'NewsHandler.php');
    post(ENDPOINT_NEWS          . '/put/$id',           PATH_HANDLERS_FOLDER . 'NewsHandler.php');
    post(ENDPOINT_LOGS,                                 PATH_HANDLERS_FOLDER . 'LogsHandler.php');
    post(ENDPOINT_PROJECTS,                             PATH_HANDLERS_FOLDER . 'ProjectsHandler.php');
    post(ENDPOINT_PROJECTS      . '/put/$id',           PATH_HANDLERS_FOLDER . 'ProjectsHandler.php');
    post(ENDPOINT_CATEGORIES,                           PATH_HANDLERS_FOLDER . 'CategoriesHandler.php');
    
    put(ENDPOINT_LIST_IP        . '/$id',               PATH_HANDLERS_FOLDER . 'ListIpHandler.php');
    put(ENDPOINT_CONTACTS       . '/$id',               PATH_HANDLERS_FOLDER . 'ContactsHandler.php');
    put(ENDPOINT_CATEGORIES     . '/$id',               PATH_HANDLERS_FOLDER . 'CategoriesHandler.php');
    put(ENDPOINT_ADMIN_PASS,                            PATH_HANDLERS_FOLDER . 'AdminHandler.php');

    delete(ENDPOINT_NEWS        . '/$id',               PATH_HANDLERS_FOLDER . 'NewsHandler.php');
    delete(ENDPOINT_LIST_IP     . '/$id',               PATH_HANDLERS_FOLDER . 'ListIpHandler.php');
    delete(ENDPOINT_PROJECTS    . '/$id',               PATH_HANDLERS_FOLDER . 'ProjectsHandler.php');
    delete(ENDPOINT_CATEGORIES  . '/$id',               PATH_HANDLERS_FOLDER . 'CategoriesHandler.php');

    echo json_encode(ResponseHttp::status400('Данный эндпоинт не существует. Проверьте запрос на корректность!'));
    exit;
    
} catch (\Exception $ex) {

    error_log("index.php -> \n$ex\n");
    die(json_encode(ResponseHttp::status500()));
}
