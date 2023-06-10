<?php

namespace App\Config;

use App\DataBase\Sql;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Security{

    private static $jwt_data;

    final public static function getSecretKey(){

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv -> load();

        return $_ENV['SECRET_KEY'];
    }

    final public static function getChatGptKey(){

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv -> load();

        return $_ENV['CHAT_GPT_API_KEY'];
    }

    final public static function getTelegramBotKey(){

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv -> load();

        return $_ENV['TELEGRAM_BOT_API_KEY'];
    }

    final public static function getTelegramChatId(){

        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv -> load();

        return $_ENV['TELEGRAM_CHAT_ID'];
    }

    final public static function createPassword(string $pw_base64){

        $pw = base64_decode($pw_base64);
        $pwh = password_hash($pw, PASSWORD_DEFAULT);
        return $pwh;
    }

    final public static function validatePassword(string $pw_base64, string $pwh){

        $pw = base64_decode($pw_base64);

        if(password_verify($pw, $pwh)) return true;
        else return false;
    }

    final public static function createTokenJwt(string $key, array $data){

        $payload = array(
            "iat" => time(),
            "exp" => time() + (60 * 60 * 24),
            "data" => $data,
        );

        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    final public static function validateTokenJwt(string $key, array $token){

        if(!isset($token['Authorization'])){

            die(json_encode(ResponseHttp::status400('Заголовок Authorization не найден!')));
            exit;
        }

        try {
            
            $jwt = explode(" ", $token['Authorization']);
            
            if(count($jwt) != 2){
                
                die(json_encode(ResponseHttp::status400('Невалидный формат заголовка Authorization')));
                exit;
            }
            else{

                $data = JWT::decode($jwt[1], new Key($key, 'HS256'));
                self::$jwt_data = $data;

                return self::$jwt_data;
                exit;
            }
        }
        catch (\Exception $e) {

            error_log('Token invalid');
            die(json_encode(ResponseHttp::status401('Невалидный токен')));
        }
    }

    final public static function getTokenJwtData(){
        
        $jwt_decoded_array = json_decode(json_encode(self::$jwt_data), true);
        return $jwt_decoded_array['data'];
        exit;
    }

    final public static function check_banned(){
        
        $ip = '';

         //Cloudflare header check
        empty($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $ip = $_SERVER['REMOTE_ADDR'] : $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        if(Sql::check_banned($ip)) die(json_encode(ResponseHttp::status401('Доступ запрещен! Ваш IP забанен!')));
    }
}