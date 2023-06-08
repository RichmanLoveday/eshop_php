<?php

use app\core\Database;
use app\core\Models;
use app\models\Auth;

class Blogs extends Models
{

    public function create($DATA, $FILES, $image_class = null)
    {
        $DB = Database::getInstance();

        $arr['title'] = ucwords(trim($DATA->title));
        $arr['post'] = ucwords(trim($DATA->post));
        $arr['user_url'] = $_SESSION['USER']->url_address;
        $arr['url_address'] = $this->str_to_url(trim($DATA->title));
        $arr['date'] = date("Y-m-d H:i:s");

        $error = false;
        // check if theirs an error in input
        if (empty($arr['title']) || !preg_match("/^[a-zA-Z 0-9._\-,]+$/", trim($arr['title']))) {
            $this->errors['errorTitle'] = 'Please enter a valid title for this post';
            $error = true;
        }

        if (empty($arr['post'])) {
            $this->errors['errorPost'] = 'Please enter a valid post';
            $error = true;
        }

        // make sure slag is unique
        $query = "SELECT url_address FROM blogs WHERE url_address = :url_address limit 1";
        $check = $DB->read($query, ['url_address' => $arr['user_url']]);

        // check if query ran
        if ($check) {
            $arr['url_address'] .= '-' . rand(0, 99999);
        }


        if (!$error) {
            // Check for files
            $arr["image"] = "";
            // $arr["image2"] = "";

            // Rules for image uploads
            $allowed = ["image/png", "image/jpeg"];

            $size = 10;
            $size = ($size * 1024 * 1024);

            $dir = "uploads/";

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }


            foreach ($FILES as $key => $img_row) {
                // var_dump($key);
                if ($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                    if ($img_row['size'] > $size) {
                        $this->errors['image_size'] = $key . " is bigger than the required size.";
                        return false;
                    }

                    $destination = $dir . $image_class->generate_filename(60) . ".jpg";
                    move_uploaded_file($img_row['tmp_name'], $destination);
                    $arr[$key] = $destination;

                    $image_class->resize_image($destination, $destination, 1500, 1500, $img_row['type']);
                }
            }

            // If no error at all insert
            $query = "INSERT INTO blogs (title, post, user_url, url_address, image, date) values (:title, :post, :user_url, :url_address, :image, :date)";
            $check = $DB->write($query, $arr);

            // check if query ran
            if (!$check) {
                $this->errors['error'] = 'Unable to post';
                return false;
            }
            $this->success_message = 'Blog added succesfully';
            return true;
        }
    }

    public function get_all($find = null)
    {
        $DB = Database::newInstance();

        $result  = '';
        if (is_null($find)) {
            $query = "SELECT * FROM blogs order by id DESC";
            $result = $DB->read($query);
        } else {
            $query = "SELECT * FROM blogs WHERE title like :title order by id DESC";
            $result = $DB->read($query, ['title' => $find]);
        }

        return $result;
    }

    public function get_single_blog($url_address)
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM blogs WHERE url_address = '$url_address' limit 1";
        $result = $DB->read($query);
        return $result;
    }

    public function edit($DATA, $FILES = null, $image_class = null)
    {
        $DB = Database::newInstance();

        $arr['id'] = (int) $DATA->id;
        $arr['title'] = ucwords(trim($DATA->title));
        $arr['post'] = ucwords(trim($DATA->post));
        $arr['url_address'] = $this->str_to_url(trim($DATA->title));

        $error = false;
        // check if theirs an error in input
        if (empty($arr['title']) || !preg_match("/^[a-zA-Z 0-9._\-,]+$/", trim($arr['title']))) {
            $this->errors['errorTitle'] = 'Please enter a valid title for this post';
            $error = true;
        }

        if (empty($arr['post'])) {
            $this->errors['errorPost'] = 'Please enter a valid post';
            $error = true;
        }


        if (!$error) {
            // Validate inmages
            // Check for images images in data if set
            $arr["image"] = "";
            // $arr["image2"] = "";

            // Rules for image uploads
            $allowed = ["image/png", "image/jpeg"];
            $size = 10;
            $size = ($size * 1024 * 1024);

            $dir = "uploads/";

            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }


            if (!is_null($FILES)) {
                foreach ($FILES as $key => $img_row) {
                    if ($img_row['error'] == 0 && in_array($img_row['type'], $allowed)) {
                        if ($img_row['size'] > $size) {
                            $this->errors['image_size'] = $key . " is bigger than the required size.";
                            return false;
                        }

                        $destination = $dir . $image_class->generate_filename(60) . ".jpg";
                        move_uploaded_file($img_row['tmp_name'], $destination);
                        $arr[$key] = $destination;
                        $image_class->resize_image($destination, $destination, 1500, 1500, $img_row['type']);
                    }
                }
            } else {
                $arr['image'] = $DATA->image;
            }


            $query = "UPDATE blogs SET title = :title, post = :post, url_address = :url_address, image = :image WHERE id = :id limit 1";
            $check = $DB->write($query, $arr);

            if (!$check) return false;

            $this->success_message = "Blog successfully edited";
            return true;
        }
    }


    public function delete($id)
    {
        $DB = Database::getInstance();
        $id = (int) $id;
        $query = "DELETE FROM blogs WHERE id = '$id' limit 1";
        $result = $DB->write($query);

        $this->success_message = "Blog Deleted Successfully";

        return $result ?? false;
    }


    public function str_to_url($url)
    {
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }
}
