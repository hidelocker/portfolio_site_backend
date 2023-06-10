<?php

use App\DataBase\ConnectionDB;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv -> load();

$data = array(
    'user' => $_ENV['DATABASE_USER'],
    'pass' => $_ENV['DATABASE_PASS'],
    'name' => $_ENV['DATABASE_NAME'],
    'host' => $_ENV['DATABASE_HOST'],
    'port' => $_ENV['DATABASE_PORT'],
);

$host = 'mysql:host=' . $data['host'] . ';port=' . $data['port'] . ';dbname=' . $data['name'] . ';';
ConnectionDB::from($host, $data['user'], $data['pass']);