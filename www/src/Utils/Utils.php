<?php

namespace App\Utils;

use App\Config\ResponseHttp;

class Utils
{
    final public static function get_path_upload()
    {

        return dirname(__DIR__) . '/Uploads/';
    }

    final public static function upload_image_save($file)
    {
        try {

            if (!is_writable(self::get_path_upload())) {

                error_log("Доступ к папке загрузки файлов запрещен! ->" . self::get_path_upload());
                die(json_encode(ResponseHttp::status500('Доступ к папке загрузки файлов запрещен!')));
            }

            else if (empty($file['name'])) die(json_encode(ResponseHttp::status400('Не обнаружено поле `name` у сохраняемого изображение!')));
            else if (empty($file['size'])) die(json_encode(ResponseHttp::status400('Не обнаружено поле `size` у сохраняемого изображение!')));
            else if (empty($file['tmp_name'])) die(json_encode(ResponseHttp::status400('Не обнаружено поле `tmp_name` у сохраняемого изображение!')));
            else {


                $image_name = $file['name'];
                $image_size = $file['size'];
                $tmp_name = $file['tmp_name'];
                $new_image_name = date('Y-m-d_h-i-s', time());
                $extensions = ['jpeg', 'jpg', 'png', 'webp'];
                $ext = pathinfo($image_name, PATHINFO_EXTENSION);

                if (!in_array($ext, $extensions)) die(json_encode(ResponseHttp::status400('Неверный формат изображения!')));
                else if ($image_size > 2048000) die(json_encode(ResponseHttp::status400('Размер изображения не должнет превышать 2мб!')));
                else {

                    $file_name = $new_image_name . '.' . $ext;
                    move_uploaded_file($tmp_name, self::get_path_upload() . $file_name);

                    return $file_name;
                }
            }
        } catch (\Exception $e) {

            error_log("Utils::upload_image_save ->\n{$e}\n\n");
            die(json_encode(ResponseHttp::status500('Произошла ошибка при сохранении изображения')));
        }
    }

    final public static function upload_image_delete($image_name)
    {
        try {
            $delete_image = self::get_path_upload() . $image_name;

            if (!file_exists($delete_image)) error_log("Utils::upload_image_delete -> Удаляемое изображение не найдено - {$delete_image}\n\n");
            else {

                unlink($delete_image);
            }
        } catch (\Exception $e) {

            error_log("Utils::upload_image_delete ->\n{$e}\n\n");
            die(json_encode(ResponseHttp::status500('Произошла ошибка при удалении изображения')));
        }
    }
}
