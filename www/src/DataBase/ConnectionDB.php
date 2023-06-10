<?php

namespace App\DataBase;

use App\Config\ResponseHttp;
use PDO;

require __DIR__ . '/dataDB.php';

class ConnectionDB{

    private static $host = '';
    private static $user = '';
    private static $pass = '';

    final public static function from($host, $user, $pass){

        self::$host = $host;
        self::$user = $user;
        self::$pass = $pass;
    }

    final public static function getConnection(){

        try{
            
            $opt = [\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC];
            $dsn = new PDO(self::$host, self::$user, self::$pass, $opt);
            $dsn -> setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $dsn;
        }
        catch (\PDOException $e){
            
            error_log("ConnectionDB::getConnection -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500('Не удалось подключиться к базе данных')));
        }
    }
}