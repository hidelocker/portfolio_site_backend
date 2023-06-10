<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;

class ContactsModel extends ConnectionDB
{

    private  int $id;
    private  string $title;
    private  string $name;
    private  string $link;

    //Getters
    final public  function getId()
    {
        return $this->id;
    }
    final public  function getTitle()
    {
        return $this->title;
    }
    final public  function getName()
    {
        return $this->name;
    }
    final public  function getLink()
    {
        return $this->link;
    }

    //Setters
    final public  function setId(int $id)
    {
        $this->id = $id;
    }
    final public  function setTitle(string $title)
    {
        $this->title = $title;
    }
    final public  function setName(string $name)
    {
        $this->name = $name;
    }
    final public  function setLink(string $link)
    {
        $this->link = $link;
    }

    final public  function get()
    {
        try {
            $sql = "SELECT * FROM contacts";
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->execute();

            $res = $query->fetchAll(\PDO::FETCH_ASSOC);
            return ResponseHttp::status200($res);
        } catch (\PDOException $e) {

            error_log("ContactsModel::get -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function put()
    {

        if (!Sql::get_field("SELECT * FROM contacts WHERE id = :id", ":id", $this->getId())) return ResponseHttp::status404('Контакт не найден');
        else {

            try {
                $sql = "UPDATE contacts SET name = :name, link = :link WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':id', $this->getId());
                $query->bindValue(':name', $this->getName());
                $query->bindValue(':link', $this->getLink());
                $query->execute();

                return ResponseHttp::status200('Контакт обновлен');
            } catch (\PDOException $e) {

                error_log("ContactsModel::put -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }
}
