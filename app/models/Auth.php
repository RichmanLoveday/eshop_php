<?php
namespace app\models;
Class Auth {

    // As row as an ID to a specific user
    public static function authenticate($url_address) {
        //show($row); die;

        // Creating a session for every user logged in
       $_SESSION['url_address'] = $url_address;

       //show($_SESSION['url_address']); die;
    }


    // Logging out a user
    public static function logout() {
        // logging out a user and unseting a user logged in
        if(isset($_SESSION['url_address'])) {
            unset($_SESSION['url_address']);
        }
    }

    // Checking if logged in
    public static function logged_in() {
        // checking if user is logged in
        if(isset($_SESSION['url_address'])) {
            return true;
        } 
        return false;

    }

}

?>