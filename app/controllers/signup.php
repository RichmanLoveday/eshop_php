<?php
use app\core\Controller;
Class Signup extends Controller {

    public function index() {
        $data = [];
        $data['page_title'] = 'Signup';

        if($_SERVER['REQUEST_METHOD'] === "POST") {
            //show($_POST);
            $user = $this->load_model('User');
            if($user->signup($_POST)) {
                $this->redirect('login');
                
            } else {
                // Error Data to be sent to view
                $data['errors'] = $user->errors;

                //show($data);
            }

            
        } 
        $this->view("signup", $data);
    }
}


?>