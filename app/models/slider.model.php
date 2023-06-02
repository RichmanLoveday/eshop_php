<?php

use app\core\Database;
use app\core\Models;
use app\models\Auth;

class Slider extends Models
{

    public function create($DATA, $FILES, $image_class = null)
    {
        $DB = Database::getInstance();

        $arr['header1_text'] = ucwords($DATA->header1_text);
        $arr['header2_text'] = ucwords($DATA->header2_text);
        $arr['link'] = ucwords($DATA->link);
        $arr['text'] = ucwords($DATA->text);

        $error = false;
        // check if theirs an error in input
        if (empty($arr['header1_text']) || !preg_match("/^[a-zA-Z 0-9._\-,]+$/", trim($arr['header1_text']))) {
            $this->errors['errorHeader1'] = 'Please enter a valid header1 text';
            $error = true;
        }

        if (empty($arr['header2_text'])) {
            $this->errors['errorHeader2'] = 'Please enter a valid header2 text';
            $error = true;
        }

        if (empty($arr['link'])) {
            $this->errors['errorLink'] = 'Please enter a valid link';
            $error = true;
        } else {
            // check if link contains a http://
            if (!strstr($arr['link'], 'Http://')) {
                $arr['link'] = 'http://' . $arr['link'];
            }
        }

        if (empty($arr['text'])) {
            $this->errors['errorText'] = 'Please enter a valid text';
            $error = true;
        }


        if (!$error) {
            // Check for files
            $arr["image"] = "";
            $arr["image2"] = "";

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
            $query = "INSERT INTO slider_images (header1_text, header2_text, text, link, image, image2) values (:header1_text, :header2_text, :text, :link, :image, :image2)";
            $check = $DB->write($query, $arr);

            // check if query ran
            if (!$check) {
                $this->errors['error'] = 'Unable to insert';
                return false;
            }
            $this->success_message = 'Slider details added succesfully';
            return true;
        }
    }

    public function get_all()
    {
        $DB = Database::newInstance();
        $query = "SELECT * FROM slider_images WHERE disabled = 0";
        $result = $DB->read($query);

        return $result;
    }
}
