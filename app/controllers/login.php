<?php

use app\models\Auth;
use app\core\Controller;

class Login extends Controller
{

    public function index()
    {
        $data = [];
        $data['page_title'] = 'Login';
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;


        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            //show($_POST);

            $user = $this->load_model('User');
            $row = $user->login($_POST);        // Get the row data when login

            if (is_array($row) && count($row) > 0) {
                $USER = $row[0];
                Auth::authenticate($USER);       // Store user row
                $this->redirect('home');        // Redirect page

            } else {
                // Error Data to be sent to view
                $data['errors'] = $user->errors;
                $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
                //show($data);
            }
        }

        $this->view("login", $data);
    }
}