<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Admin extends Controller {

    public function index() {
        $data = [];
        
        $url = Auth::logged_in();
        if(!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin',
            'user_data' => $row,
        ];
    
        $this->view("admin/index", $data);         
    }


    public function categories() {
        $data = [];
        
        $url = Auth::logged_in();
        if(!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin',
            'user_data' => $row,
        ];
    
        $this->view("admin/categories", $data);         
    }
}


?>