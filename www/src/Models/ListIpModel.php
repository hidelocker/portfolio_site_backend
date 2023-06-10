<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;

class ListIpModel extends ConnectionDB
{

    private  int $id;
    private  string $ip;
    private  string $first_visit;
    private  string $last_visit;
    private  string $status_blocking_fk;

    //Getters
    final public  function getId()
    {
        return $this ->id;
    }
    final public  function getIp()
    {
        return $this ->ip;
    }
    final public  function getFirstVisit()
    {
        return $this ->first_visit;
    }
    final public  function getLastVisit()
    {
        return $this ->last_visit;
    }
    final public  function getStatusBlocking()
    {
        return $this ->status_blocking_fk;
    }

    //Setters
    final public  function setId(int $id)
    {
        $this ->id = $id;
    }
    final public  function setIp(string $ip)
    {
        $this ->ip = $ip;
    }
    final public  function setFirstVisit(string $first_visit)
    {
        $this ->first_visit = $first_visit;
    }
    final public  function setLastVisit(string $last_visit)
    {
        $this ->last_visit = $last_visit;
    }
    final public  function setStatusBlocking(string $status_blocking_fk)
    {
        $this ->status_blocking_fk = $status_blocking_fk;
    }

    final public  function get($page)
    {

        try {

            $limit = LIMIT_RETURN_LIST_IP;
            $offset = ($limit * $page) - $limit;

            //Получаем количество всех записей, если записей нет получаем false
            $row_counts = Sql::get_field("SELECT COUNT(*) AS counts FROM list_ip");

            if (!$row_counts || $row_counts['counts'] == 0) return ResponseHttp::status200(array('list_ip' => []));
            else {

                $row_counts = $row_counts['counts'];
                $page_counts = ceil($row_counts / $limit);

                $sql = "SELECT list_ip.id, list_ip.ip, list_ip.first_visit, list_ip.last_visit, status_blocking.name AS status_blocking FROM list_ip LEFT JOIN status_blocking ON list_ip.status_blocking_fk = status_blocking.id ORDER BY id DESC LIMIT :limit OFFSET :offset";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
                $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
                $query->execute();

                $res['info']['total_pages'] = "{$page_counts}";
                $res['info']['current_page'] = $page;
                $res['list_ip'] = $query->fetchAll(\PDO::FETCH_ASSOC);

                return ResponseHttp::status200($res);
            }
        } catch (\PDOException $e) {

            error_log("ListIpModel::get -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function put()
    {
        //Проверяем есть ли такой статус блокировки, если есть получаем ID статуса, иначе false
        $status_blocking = Sql::get_field("SELECT id FROM status_blocking WHERE name = :name", ":name", $this ->getStatusBlocking());

        if (!Sql::get_field("SELECT * FROM list_ip WHERE id = :id", ":id", $this ->getId())) return ResponseHttp::status404('IP не найден!');
        else if (!$status_blocking) return ResponseHttp::status400("Такой статус блокировки не найден!");
        else {
            try {

                $this ->setStatusBlocking($status_blocking['id']);

                $sql = "UPDATE list_ip SET ip = :ip, last_visit = :last_visit, status_blocking_fk = :status_blocking_fk WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':id', $this->getId());
                $query->bindValue(':ip', $this->getIp());
                $query->bindValue(':last_visit', $this->getLastVisit());
                $query->bindValue(':status_blocking_fk', $this->getStatusBlocking());
                $query->execute();

                return ResponseHttp::status200('IP обновлен');
            } catch (\PDOException $e) {

                error_log("ListIpModel::put -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }

    final public  function delete()
    {

        if (!Sql::get_field("SELECT * FROM list_ip WHERE id = :id", ":id", $this ->getId())) return ResponseHttp::status404('IP не найден');
        else {
            try {

                $sql = "DELETE FROM list_ip WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);
                
                $query->bindValue(':id', $this->getId());
                $query->execute();

                if ($query->rowCount() == 0) die(json_encode(ResponseHttp::status500()));
                else return ResponseHttp::status200('IP удален');
            } catch (\PDOException $e) {

                error_log("ListIpModel::delete -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }
}
