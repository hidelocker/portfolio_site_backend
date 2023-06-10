<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\NewsModel;

class NewsController
{

    private $method;
    private $input_data;
    private $post_data;
    private $get_data;
    private $headers;

    private static $validate_num = '/^\d+$/';
    private static $validate_text = '/^[а-яА-ЯёЁa-zA-Z0-9?,.+_@=*:\-\/()^\s]+$/iu';

    public function __construct($method, $headers, $input_data, $post_data, $get_data)
    {

        $this->method = $method;
        $this->headers = $headers;
        $this->input_data = $input_data;
        $this->post_data = $post_data;
        $this->get_data = $get_data;
    }

    final public function single($id)
    {
        if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
        else {

            $model = new NewsModel();
            $model->setId($id);
            
            echo json_encode($model->single($id));
        }

        exit;
    }

    final public function get($page)
    {
        if (!preg_match(self::$validate_num, $page)) echo json_encode(ResponseHttp::status400('Невалидное поле - page'));
        else {

            $model = new NewsModel();
            echo json_encode($model->get($page));
        }

        exit;
    }

    final public function post()
    {
        if (empty($_FILES['image']) || empty($this->post_data['title']) || empty($this->post_data['body']) || empty($this->post_data['categories'])) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (image, title, body, categories)'));
        else if (mb_strlen($this->post_data['title']) > 80) echo json_encode(ResponseHttp::status400('Длина заголовка должна быть меньше 80 символов'));
        else if (!preg_match(self::$validate_text, $this->post_data['title'])) echo json_encode(ResponseHttp::status400('Невалидное поле - title'));
        else if (!preg_match(self::$validate_text, $this->post_data['categories'])) echo json_encode(ResponseHttp::status400('Невалидное поле - categories'));
        else if (strtolower($this->post_data['categories']) == "all") echo json_encode(ResponseHttp::status400('Категорие не может быть - all'));
        else {

            $model = new NewsModel();
            $model->setImage($_FILES['image']);
            $model->setTitle($this->post_data['title']);
            $model->setBody($this->post_data['body']);
            $model->setCategories($this->post_data['categories']);
            $model->setCreatedAt(date('d.m.Y', time()));

            echo json_encode($model->post());
        }

        exit;
    }

    final public function put(string $id)
    {
        if (empty($this->post_data['title']) || empty($this->post_data['body']) || empty($this->post_data['categories']) || empty($id)) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (id, image (null), title, body, categories)'));
        else if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
        else if (!preg_match(self::$validate_text, $this->post_data['title'])) echo json_encode(ResponseHttp::status400('Невалидное поле - title'));
        else if (!preg_match(self::$validate_text, $this->post_data['body'])) echo json_encode(ResponseHttp::status400('Невалидное поле - body'));
        else if (!preg_match(self::$validate_text, $this->post_data['categories'])) echo json_encode(ResponseHttp::status400('Невалидное поле - categories'));
        else if (strtolower($this->post_data['categories']) == "all") echo json_encode(ResponseHttp::status400('Категорие не может быть - all'));
        else {

            $model = new NewsModel();
            $model->setId($id);
            $model->setImage(!empty($_FILES['image']) ? $_FILES['image'] : null);
            $model->setTitle($this->post_data['title']);
            $model->setBody($this->post_data['body']);
            $model->setCategories($this->post_data['categories']);

            echo json_encode($model->put());
        }

        exit;
    }

    final public function delete($id)
    {

        if (empty($id)) echo json_encode(ResponseHttp::status400('Присутствуют пустые поля (id)'));
        else if (!preg_match(self::$validate_num, $id)) echo json_encode(ResponseHttp::status400('Невалидное поле - id'));
        else {

            $model = new NewsModel();
            $model->setId($id);

            echo json_encode($model->delete());
        }

        exit;
    }
}
