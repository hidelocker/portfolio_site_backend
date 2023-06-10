<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;

class CategoriesModel extends ConnectionDB
{

    private  int $id;
    private  string $name;

    //Getters
    final public  function getId()
    {
        return $this->id;
    }
    final public  function getName()
    {
        return $this->name;
    }

    //Setters
    final public  function setId(int $id)
    {
        $this->id = $id;
    }
    final public  function setName(string $name)
    {
        $this->name = $name;
    }

    final public  function get()
    {

        try {

            $sql = "SELECT * FROM categories";
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->execute();

            $res = $query->fetchAll(\PDO::FETCH_ASSOC);
            return ResponseHttp::status200($res);
        } catch (\PDOException $e) {

            error_log("CategoriesModel::get -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function post()
    {
        try {

            $sql = "INSERT INTO categories(name) VALUES(:name)";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':name', $this->getName());
            $query->execute();

            return ResponseHttp::status201('Категория создана');
        } catch (\PDOException $e) {

            error_log("CategoriesModel::put -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function put()
    {
        try {

            $sql = "UPDATE categories SET name = :name WHERE id = :id";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':id', $this->getId());
            $query->bindValue(':name', $this->getName());
            $query->execute();

            return ResponseHttp::status200('Категория обновлена');
        } catch (\PDOException $e) {

            error_log("CategoriesModel::put -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function delete()
    {

        try {

            $sql = "DELETE FROM categories WHERE id = :id";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':id', $this->getId());
            $query->execute();

            if ($query->rowCount() == 0) die(json_encode(ResponseHttp::status500()));
            else return ResponseHttp::status200('Категория удалена');
        } catch (\PDOException $e) {

            error_log("CategoriesModel::delete -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
