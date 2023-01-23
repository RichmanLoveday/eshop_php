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
                // session_destroy();
                show($row);
                // die;
                $url_address = $row[0]->url_address;
                show($url_address);
                show(Auth::class);
                // die;
                Auth::authenticate($url_address);       // Store user url_address
                
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