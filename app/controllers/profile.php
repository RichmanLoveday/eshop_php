<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

Class Profile extends Controller {

    public function index() {
        $data = [];
        
        $USER = Auth::logged_in();
        //show($USER); die;
        if(!$USER) $this->redirect('login');         // Redirect user to login

        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($USER);

        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_title' => 'Profile',
            'user_data' => $row,
        ];
    
        $this->view("profile", $data);         
    }
}


?>