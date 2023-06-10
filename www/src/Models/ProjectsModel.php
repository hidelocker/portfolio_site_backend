<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DataBase\ConnectionDB;
use App\DataBase\Sql;
use App\Utils\Utils;
use DateTime;

class ProjectsModel extends ConnectionDB
{

    private  int $id;
    private  $image;
    private  string $title;
    private  string $body;
    private  string $github;
    private  string $stack;
    private  string $categories_fk;
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
    final public  function getGitHub()
    {
        return $this->github;
    }
    final public  function getStack()
    {
        return $this->stack;
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
    final public function setId(int $id)
    {
        $this->id = $id;
    }
    final public function setImage($image)
    {
        $this->image = $image;
    }
    final public function setTitle(string $title)
    {
        $this->title = $title;
    }
    final public function setBody(string $body)
    {
        $this->body = $body;
    }
    final public function setGitHub(string $github)
    {
        $this->github = $github;
    }
    final public function setStack(string $stack)
    {
        $this->stack = $stack;
    }
    final public function setCategories($categories_fk)
    {
        $this->categories_fk = $categories_fk;
    }
    final public function setCreatedAt(string $createdAt)
    {
        $this->createdAt = $createdAt;
    }


    final public function get($page)
    {

        try {

            $limit = LIMIT_RETURN_PROJECTS;
            $offset = ($limit * $page) - $limit;

            //Получаем количество всех записей
            $row_counts = Sql::get_field("SELECT COUNT(*) AS counts FROM projects");

            $row_counts = $row_counts['counts'] == 0 ? 1 : $row_counts['counts'];
            $page_counts = ceil($row_counts / $limit);

            $sql = "SELECT projects.id, projects.image, projects.title, projects.body, projects.stack, projects.github, categories.name AS categories, projects.createdAt FROM projects LEFT JOIN categories ON projects.categories_fk = categories.id ORDER BY id DESC LIMIT :limit OFFSET :offset";
            $con = self::getConnection();
            $query = $con->prepare($sql);

            $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $query->execute();

            $res['info']['total_pages'] = intval($page_counts);
            $res['info']['current_page'] = intval($page);
            $res['projects'] = $query->fetchAll(\PDO::FETCH_ASSOC);

            return ResponseHttp::status200($res);
        } catch (\PDOException $e) {

            error_log("ProjectsModel::get -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public function post()
    {
        try {

            //Проверяем есть ли такая категория, если есть получаем ID категории, иначе false
            $categories = Sql::get_field("SELECT id FROM categories WHERE name = :name", ":name", $this->getCategories());

            if (!$categories) return ResponseHttp::status400("Такой категории не найдено");
            else {
                $this->setCategories($categories['id']);

                $sql = "INSERT INTO projects(image, title, body, stack, github, categories_fk, createdAt) VALUES(:image, :title, :body, :stack, :github, :categories_fk, :createdAt)";
                $con = self::getConnection();
                $query = $con->prepare($sql);


                $query->bindValue(':image', Utils::upload_image_save($this->getImage()));
                $query->bindValue(':title', $this->getTitle());
                $query->bindValue(':body', $this->getBody());
                $query->bindValue(':stack', $this->getStack());
                $query->bindValue(':github', $this->getGitHub());
                $query->bindValue(':categories_fk', $this->getCategories());
                $query->bindValue(':createdAt', $this->getCreatedAt());
                $query->execute();

                return ResponseHttp::status201('Проект создан');
            }
        } catch (\PDOException $e) {

            error_log("ProjectsModel::post -> \n" . $e . "\n\n");
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public function put()
    {

        //Проверяем есть ли такая категория, если есть получаем ID категории, иначе false
        $categories = Sql::get_field("SELECT id FROM categories WHERE name = :name", ":name", $this->getCategories());

        if (!Sql::get_field("SELECT id FROM projects WHERE id = :id", ":id", $this->getId())) return ResponseHttp::status404('Проект не найден!');
        else if (!$categories) return ResponseHttp::status400("Такой категории не найдено");
        else {
            $this->setCategories($categories['id']);

            try {

                $sql = "UPDATE projects SET title = :title, body = :body, github = :github, stack = :stack, categories_fk = :categories_fk WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                //Если передали так же изображение, то меняем sql запрос, загружаем новое изображение и удаляем старое
                if ($this->getImage() !== null) {


                    $old_image = Sql::get_field("SELECT image FROM projects WHERE id = :id", ":id", $this->getId());
                    $sql = "UPDATE projects SET image = :image, title = :title, body = :body, github = :github, stack = :stack, categories_fk = :categories_fk WHERE id = :id";
                    $query = $con->prepare($sql);

                    $query->bindValue(':image', Utils::upload_image_save($this->getImage()));

                    if (!$old_image) return ResponseHttp::status500("Не удалось обновить изображение!");
                    Utils::upload_image_delete($old_image['image']);
                }

                $query->bindValue(':id', $this->getId());
                $query->bindValue(':title', $this->getTitle());
                $query->bindValue(':body', $this->getBody());
                $query->bindValue(':github', $this->getGitHub());
                $query->bindValue(':stack', $this->getStack());
                $query->bindValue(':categories_fk', $this->getCategories());
                $query->execute();


                return ResponseHttp::status200('Проект обновлен');
            } catch (\PDOException $e) {

                error_log("ProjectsModel::put -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }

    final public function delete()
    {

        $image_delete = Sql::get_field("SELECT image FROM projects WHERE id = :id", ":id", $this->getId());
        if (!Sql::get_field("SELECT id FROM projects WHERE id = :id", ":id", $this->getId())) return ResponseHttp::status404('Проект не найден!');
        else {
            try {

                $sql = "DELETE FROM projects WHERE id = :id";
                $con = self::getConnection();
                $query = $con->prepare($sql);

                $query->bindValue(':id', $this->getId());
                $query->execute();

                if ($query->rowCount() <= 0) die(json_encode(ResponseHttp::status500()));
                else {

                    //Удаляем из сервера изображение проекта
                    Utils::upload_image_delete($image_delete['image']);

                    return ResponseHttp::status200('Проект удален');
                }
            } catch (\PDOException $e) {

                error_log("ProjectsModel::delete -> \n" . $e . "\n\n");
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }
}
