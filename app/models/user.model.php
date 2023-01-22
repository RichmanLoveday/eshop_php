<?php

Class User {
    private $error = '';

    public function signup($POST) {
        //show($POST);
        $data = [];

        $data['name']       = $_POST['name'];
        $data['email']      = $_POST['email'];
        $data['password']   = trim($_POST['password']);
        $conPass            = trim($_POST['confirm_password']);

        // Vaidate email
        if(empty($data['email']) || !preg_match("/^[a-zA-Z_-]+@[a-zA-Z]+.[a-zA-Z]+$/", $data['email'])) {
            $this->error .= "Please enter a valid email <br>";
        }

        // validate name
        if(empty($data['name']) || !preg_match("/^[a-zA-Z]+$/", $data['name'])) {
            $this->error .= "Please enter a valid name <br>";
        }

        // Validate password
        if($data['password'] !== $conPass) {
            $this->error .= "Password do not match <br>";
        }

        if(strlen($data['password']) < 4) {
            $this->error .= "Passwod must be atleat 4 character long <br>";
        }

        // show($data);
        // echo $this->error;
        // die;


        // Save to database
        if($this->error === "") {
            $data['rank'] = "customer";
            $data['url_address'] = get_random_string(60);
            $data['date'] = date("Y-m-d H:i:s");

            // show($data);
            // die;

            $query = "INSERT INTO users (url_address, name, email, password, rank) values (:url_address, :name, :email, :password, :rank)";
            $db = Database::getInstance();
            $result = $db->write($query, $data);
            show($result);
            if($result) {
                die;
                header("Location: " . ROOT . "login");
                die;
            }
        }
        
    }

    public function login($Post) {

    }

    public function get_user($url) {

    }


}