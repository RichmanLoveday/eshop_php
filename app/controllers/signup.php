<?php

use app\core\Controller;

class Signup extends Controller
{

    public function index()
    {
        $data = [];
        $data['page_title'] = 'Signup';
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            //show($_POST);
            $user = $this->load_model('User');
            if ($user->signup($_POST)) {
                $this->redirect('login');
            } else {
                // Error Data to be sent to view
                $data['errors'] = $user->errors;
                $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
                //show($data);
            }
        }
        $this->view("signup", $data);
    }
}