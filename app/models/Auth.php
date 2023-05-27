<?php

namespace app\models;

class Auth
{
    // As row as an ID to a specific user
    public static function authenticate($USER)
    {
        //show($row); die;

        // Creating a session for every user logged in
        $_SESSION['USER'] = $USER;

        //show($_SESSION['USER']); die;
    }


    // Logging out a user
    public static function logout()
    {
        // logging out a user and unseting a user logged in
        if (isset($_SESSION['USER'])) {
            unset($_SESSION['USER']);
            unset($_SESSION['CART']);
        }
    }

    // Checking if logged in
    public static function logged_in()
    {
        // checking if user is logged in
        if (isset($_SESSION['USER'])) {
            return $_SESSION['USER'];
        }
        return false;
    }



    // Access to different functionalities
    public static function access(string $rank = 'customer'): bool
    {

        // 
        if (!isset($_SESSION['USER'])) {
            return false;
        }

        // Checking if rank is logged in
        $loged_in_rank = $_SESSION['USER']->rank;

        // Array of users access
        $RANK['admin']          = ['admin', 'reception', 'customer'];
        $RANK['reception']      = ['reception', 'customer'];
        $RANK['customer']       = ['customer'];

        // if the login user in not set
        if (!isset($RANK[$loged_in_rank])) {
            return false;
        }

        // checking if selected rank is in array
        if (in_array($rank, $RANK[$loged_in_rank])) {
            return true;
        }
        return false;
    }
}
