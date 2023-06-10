<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;
use App\Utils\Utils;
use DateTime;

class NewsModel extends ConnectionDB
{

    private  int $id;
    private  $image;
    private  string $title;
    private  string $body;
    private  $categories_fk;
    private  string $createdAt;

    //Getters
    final public  function getId()
    {
        return $this->id;
    }
    final public  function getImage()
    {
        return $this->image;
    }
    final public  function getTitle()
    {
        return $this->title;
    }
    final public  function getBody()
    {
        return $this->body;
    }
    final public  function getCategories()
    {
        return $this->categories_fk;
    }
    final public  function getCreatedAt()
    {
        return $this->createdAt;
    }

    //Setters
    final public  function setId(int $id)
    {
        $this->id = $id;
    }
    final public  function setImage($image)
    {
        $this->image = $image;
    }
    final public  function setTitle(string $title)
    {
        $this->title = $title;
    }
    final public  function setBody(string $body)
    {
        $this->body = $body;
    }
    final public  function setCategories($categories_fk)
    {
        $this->categories_fk = $categories_fk;
    }
    final public  function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    final public  function single()
    {
        try {
            $sql = "SELECT * FROM news WHERE id = :id";
            $con = self::getConnection();
            $query = $con->prepare($sql);
            $query->bindValue(':id', $this -> getId(), \PDO::PARAM_INT);
            $query->execute();

            if ($query->rowCount() <= 0) die(json_encode(ResponseHttp::status404()));
            else{

                $res = $query->fetch(\PDO::FETCH_ASSOC);
                return ResponseHttp::status200($res);
            }
        } catch (\PDOException $e) {

            error_log("NewsModel::single -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function get($page)
    {

        try {

            $limit = LIMIT_RETURN_NEWS;
            $offset = ($limit * $page) - $limit;

            //Получаем количество всех записей
            $row_counts = Sql::get_field("SELECT COUNT(*) AS counts FROM news");

            $row_counts = $row_counts['counts'] == 0 ? 1 : $row_counts['counts'];
            $page_counts = ceil($row_counts / $limit);

            $sql = "SELECT news.id, news.image, news.title, news.body, categories.name AS categories, news.createdAt FROM news LEFT JOIN categories ON news.categories_fk = categories.id ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $query->execute();

            $res['info']['total_pages'] = intval($page_counts);
            $res['info']['current_page'] = intval($page);
            $res['news'] = $query->fetchAll(\PDO::FETCH_ASSOC);

            return ResponseHttp::status200($res);
        } catch (\PDOException $e) {

            error_log("NewsModel::get -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }



    final public  function post()
    {

        try {

            //Проверяем есть ли такая категория, если есть получаем ID категории, иначе false
            $categories = Sql::get_field("SELECT id FROM categories WHERE name = :name", ":name", $this->getCategories());

            if (!$categories) return ResponseHttp::status400("Такой категории не найдено");
            else {
                $this->setCategories($categories['id']);

                $sql = "INSERT INTO news(image, title, body, categories_fk, createdAt) VALUES(:image, :title, :body, :categories_fk, :createdAt)";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':image', Utils::upload_image_save($this->getImage()));
                $query->bindValue(':title', $this->getTitle());
                $query->bindValue(':body', $this->getBody());
                $query->bindValue(':categories_fk', $this->getCategories());
                $query->bindValue(':createdAt', $this->getCreatedAt());


                $query->execute();

                return ResponseHttp::status201('Новость создана');
            }
        } catch (\PDOException $e) {

            error_log("NewsModel::post -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public  function put()
    {
        //Проверяем есть ли такая категория, если есть получаем ID категории, иначе false
        $categories = Sql::get_field("SELECT id FROM categories WHERE name = :name", ":name", $this->getCategories());
        $this->setCategories($categories['id']);


        if (!Sql::get_field("SELECT * FROM news WHERE id = :id", ":id", $this->getId())) return ResponseHttp::status404('Новость не найден!');
        else if (!$this->getCategories()) return ResponseHttp::status400("Такой категории не найдено");
        else {
            try {

                $sql = "UPDATE news SET title = :title, body = :body, categories_fk = :categories_fk WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                //Если передали так же изображение, то меняем sql запрос, загружаем новое изображение и удаляем старое
                if ($this->getImage() !== null) {


                    $old_image = Sql::get_field("SELECT image FROM news WHERE id = :id", ":id", $this->getId());
                    $sql = "UPDATE news SET image = :image, title = :title, body = :body, categories_fk = :categories_fk WHERE id = :id";
                    $query = $con->prepare($sql);

                    $query->bindValue(':image', Utils::upload_image_save($this->getImage()));

                    if (!$old_image) return ResponseHttp::status500("Не удалось обновить изображение!");
                    Utils::upload_image_delete($old_image['image']);
                }

                $query->bindValue(':id', $this->getId());
                $query->bindValue(':title', $this->getTitle());
                $query->bindValue(':body', $this->getBody());
                $query->bindValue(':categories_fk', $this->getCategories());
                $query->execute();

                return ResponseHttp::status200('Новость обновлена');
            } catch (\PDOException $e) {

                error_log("NewsModel::put -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }

    final public  function delete()
    {

        $image_delete = Sql::get_field("SELECT image FROM news WHERE id = :id", ":id", $this->getId());
        if (!Sql::get_field("SELECT * FROM news WHERE id = :id", ":id", $this->getId())) return ResponseHttp::status404('Новость не найден!');
        else {
            try {

                $sql = "DELETE FROM news WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':id', $this->getId());
                $query->execute();

                if ($query->rowCount() <= 0) die(json_encode(ResponseHttp::status500()));
                else {

                    //Удаляем из сервера изображение проекта
                    Utils::upload_image_delete($image_delete['image']);

                    return ResponseHttp::status200('Новость удалена!');
                }
            } catch (\PDOException $e) {

                error_log("NewsModel::delete -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }
}
