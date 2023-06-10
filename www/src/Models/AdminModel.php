<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;

class AdminModel extends ConnectionDB
{

    private  string $login;
    private  string $pwh;
    private  string $id_token;
    private  string $last_updated;


    //Getters
    final public  function getLogin()
    {
        return $this->login;
    }
    final public  function getPass()
    {
        return $this->pwh;
    }
    final public  function getIdToken()
    {
        return $this->id_token;
    }
    final public  function getLastUpdated()
    {
        return $this->last_updated;
    }

    //Setters
    final public  function setLogin(string $login)
    {
        $this->login = $login;
    }
    final public  function setPass(string $pass)
    {
        $this->pwh = $pass;
    }
    final public  function setIdToken(string $id_token)
    {
        $this->id_token = $id_token;
    }
    final public  function setLastUpdated(string $last_updated)
    {
        $this->last_updated = $last_updated;
    }

    final public  function login()
    {
        try {
            //Проверяем есть ли такой логин, если есть получаем данные, иначе false
            $user = Sql::get_field("SELECT * FROM admin WHERE login = :login", ":login", $this->getLogin());

            if (!$user) return ResponseHttp::status400('Такого логина не существует!');
            else {

                if (!Security::validatePassword($this->getPass(), $user['pwh'])) return ResponseHttp::status400('Неправильный пароль!');
                else {

                    $payload = array('id_token' => $user['id_token']);
                    $token = Security::createTokenJwt(Security::getSecretKey(), $payload);

                    $data = [
                        'login' => $user['login'],
                        'token' => $token
                    ];

                    return ResponseHttp::status200($data);
                    exit;
                }
            }
        } catch (\PDOException $e) {

            error_log("UserModel::login -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function put()
    {

        try {
            $sql = "UPDATE admin SET pwh = :pwh, last_updated = :last_updated WHERE id_token = :id_token";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':pwh', Security::createPassword($this->getPass()));
            $query->bindValue(':id_token', $this->getIdToken());
            $query->bindValue(':last_updated', $this->getLastUpdated());
            $query->execute();

            if ($query->rowCount() > 0) return ResponseHttp::status200('Пароль обновлен');
            else return ResponseHttp::status400('Invalid token');

        } catch (\PDOException $e) {

            error_log("UserModel::put -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function validatePassword(string $id_token, $old_pass)
    {

        try {

            $con = self::getConnection();
            $query = $con->prepare("SELECT pwh FROM admin WHERE id_token = :id_token");
            
            $query->bindValue(':id_token', $id_token);
            $query->execute();

            if ($query->rowCount() === 0) die(json_encode(ResponseHttp::status500()));
            else {

                $res = $query->fetch(\PDO::FETCH_ASSOC);

                if (Security::validatePassword($old_pass, $res['pwh'])) return true;
                else return false;
            }
        } catch (\PDOException $e) {

            error_log("UserModel::validatePassword -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
