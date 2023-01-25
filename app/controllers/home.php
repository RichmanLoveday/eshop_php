<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Home extends Controller {

    public function index() {
        $data = [];
        
        $USER = Auth::logged_in();
        //show($USER); die;
        if(!$USER) return $this->view("index", $data);         // Redirect to index page  

        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($USER);

        if(!$row) return $this->view("index", $data);         // Redirect to index page   

        // Data to send to view
        $data = [
            'page_tittle' => 'Home',
            'user_data' => $row,
        ];
    
        $this->view("index", $data);         
    }
}


?>