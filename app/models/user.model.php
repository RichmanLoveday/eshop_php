<?php

use app\core\Database;
use app\core\Models;

class User extends Models
{

    public function signup($POST)
    {
        // connection
        $db = Database::getInstance();

        // show($POST);
        $data = [];

        $data['name']       = $POST['name'];
        $data['email']      = $POST['email'];
        $data['password']   = trim($POST['password']);
        $conPass            = trim($POST['confirm_password']);

        // Vaidate email
        if (empty($data['email']) || !preg_match("/^[0-9a-zA-Z_-]+@[a-zA-Z]+.[a-zA-Z]+$/", $data['email'])) {
            $this->errors['email'] = "Please enter a valid email <br>";
        } else {
            // check if email exist
            $sql = "SELECT * FROM users WHERE email = :email limit 1";
            $check = $db->read($sql, ['email' => $data['email']]);
            // show($check);

            if (is_array($check)) {
                $this->errors['email'] = "That email is already in use";
            }
        }

        // validate name
        if (empty($data['name']) || !preg_match("/^[a-zA-Z]+$/", $data['name'])) {
            $this->errors['name'] = "Please enter a valid name <br>";
        }

        // Validate password
        if (empty($data['password'])) {
            $this->errors['password'] = "Please enter this filled <br>";
        } elseif (strlen($data['password']) < 4) {
            $this->errors['password'] = "Passwod must be atleat 4 character long <br>";
        } elseif ($data['password'] !== $conPass) {
            $this->errors['conpass'] = "Password do not match <br>";
        } else {
            echo '';
        }


        // Check URL
        $data['url_address'] = get_random_string(60);
        $sql = "SELECT * FROM users WHERE url_address = :url_address limit 1";
        $check = $db->read($sql, ['url_address' => $data['url_address']]);

        if (is_array($check)) {
            $data['url_address'] = get_random_string(60);
        }

        // Save to database
        if (empty($this->errors)) {
            $data['rank'] = "customer";
            $data['date'] = date("Y-m-d H:i:s");
            $data['password'] = hash('sha1', $data['password']);

            // Insert into database
            $query = "INSERT INTO users (url_address, name, email, password, rank, date) values (:url_address, :name, :email, :password, :rank, :date)";
            $db->write($query, $data);

            return true;
        }

        return false;
    }

    public function login($POST)
    {
        // connection
        $db = Database::getInstance();

        // show($POST);
        $data = [];

        $data['email']      = $POST['email'];
        $data['password']   = $POST['password'];

        // Vaidate email
        if (empty($data['email'])) {
            $this->errors['email'] = "Please fill in this field <br>";
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Please fill in this field <br>";
        } elseif (strlen($data['password']) < 4) {
            $this->errors['password'] = "Passwod must be atleat 4 character long <br>";
        } else {
            echo '';
        }


        if (empty($this->errors)) {

            // check if details exist
            $sql = "SELECT * FROM users WHERE email = :email AND password = :password limit 1";
            $row = $db->read($sql, ['email' => $data['email'], 'password' => hash("sha1", $data['password'])]);

            if (is_array($row)) {
                return $row;
            } else {
                $this->errors['email/password'] = "Password / Email is incorrect";
            }
        }
        return false;
    }

    public function get_user($user_url)
    {
        $db = Database::newInstance();

        $query = "SELECT * FROM users WHERE url_address = :url limit 1";
        $row = $db->read($query, [':url' => $user_url]);

        return ($row) ? $row[0] : false;
    }

    public function get_user_row($USER)
    {
        //show($USER);
        $db = Database::newInstance();      // Database instance

        $url = $USER->url_address;
        $query = "SELECT * FROM users WHERE url_address = :url_address limit 1";
        $row = $db->read($query, [':url_address' => $url]);

        if (is_array($row)) return $row[0];

        if (!$row) return false;
    }

    public function get_user_by_admin($url_address)
    {


        $db = Database::newInstance();      // Database instance

        $query = "SELECT * FROM users WHERE url_address = :url limit 1";
        $row = $db->read($query, [':url' => $url_address]);

        if (!$row) return false;

        return $row[0];
    }

    public function get_customers()
    {
        $db = Database::newInstance();

        $arr['rank'] = 'customer';
        $query = "SELECT * FROM users WHERE rank = :rank";
        $row = $db->read($query, $arr);

        return $row ?? false;
    }

    public function get_admins()
    {
        $db = Database::newInstance();

        $arr['rank'] = 'admin';
        $query = "SELECT * FROM users WHERE rank = :rank";
        $row = $db->read($query, $arr);

        return $row ?? false;
    }
}
