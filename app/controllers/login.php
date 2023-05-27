<?php
use app\models\Auth;
use app\core\Controller;
class Login extends Controller  {
    
    public function index() {
        $data = [];
        $data['page_title'] = 'Login';

        
        if($_SERVER['REQUEST_METHOD'] === "POST") {
            //show($_POST);
            
            $user = $this->load_model('User');
            $row = $user->login($_POST);        // Get the row data when login

            if(is_array($row) && count($row) > 0) {
                $USER = $row[0];
                Auth::authenticate($USER);       // Store user row
                $this->redirect('home');        // Redirect page
                
            } else {
                // Error Data to be sent to view
                $data['errors'] = $user->errors;
                //show($data);
            }

        } 

        $this->view("login", $data);
    }

}


?>