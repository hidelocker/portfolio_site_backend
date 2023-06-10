<?php

namespace App\DataBase;

use App\Config\ResponseHttp;

class Sql extends ConnectionDB
{

    public static function get_field(string $request, string $param = null, $value = null)
    {

        try {

            $con = self::getConnection();
            $query = $con->prepare($request);
            if ($param != null && $value != null) $query->bindValue($param, $value);

            $query->execute();

            if ($query->rowCount() <= 0) return false;
            else {

                $res = $query->fetch();
                return $res;
            }
        } catch (\PDOException $e) {

            error_log("Sql::get_field -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }


    public static function check_banned(string $ip)
    {

        try {

            $sql = "SELECT list_ip.id, list_ip.ip, list_ip.first_visit, list_ip.last_visit, status_blocking.name AS status_blocking FROM list_ip LEFT JOIN status_blocking ON list_ip.status_blocking_fk = status_blocking.id WHERE list_ip.ip = :ip";
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->bindValue(':ip', $ip);
            $query->execute();

            if ($query->rowCount() > 0) {

                $res = $query->fetch(\PDO::FETCH_ASSOC);
                if ($res['status_blocking'] == 'denied') return true;
                else {

                    $query = $con->prepare("UPDATE list_ip SET last_visit = :last_visit WHERE ip = :ip");
                    $query->bindValue(':ip', $res['ip']);
                    $query->bindValue(':last_visit', date('d.m.Y h:i:s', time()));
                    $query->execute();

                    return false;
                }
            } else {

                $sql = "INSERT INTO list_ip(ip, first_visit, last_visit, status_blocking_fk) VALUES(:ip, :first_visit, :last_visit, :status_blocking_fk)";
                $con = self::getConnection();
                $query = $con->prepare($sql);
                $query->bindValue(':ip', $ip);
                $query->bindValue(':first_visit', date('d.m.Y h:i:s', time()));
                $query->bindValue(':last_visit', date('d.m.Y h:i:s', time()));
                $query->bindValue(':status_blocking_fk', 1);
                $query->execute();

                return false;
            }
        } catch (\PDOException $e) {

            error_log("Sql::check_banned -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
