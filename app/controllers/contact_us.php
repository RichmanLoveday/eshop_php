<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Contact_us extends Controller
{

    public function index()
    {
        // load model
        $Message = $this->load_model('message');
        // authenticate user

        $data['page_title'] = 'Contact Us';
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
        $data['show_search'] = false;

        if (count($_POST) > 0) {
            $contact = $Message->create((object) $_POST);

            $data['errors'] = $Message->errors;

            if ($contact) {
                $this->redirect('contact_us?success=true');
                //$data['message'] = $Message->success_message;
            }
        }

        $this->view("contact-us", $data);
    }
}
