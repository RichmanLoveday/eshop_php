<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Products extends Controller {

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


    public function products() {
        $data = [];
        
        $url = Auth::logged_in();
        if(!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        //  Load user data
        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        // Load categories 
        $products = $this->load_model('Product');
        $item = $products->get_all_data();

        // load products table
        if(is_array($item)) $table_row = $products->make_table($item);
        

        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_title' => 'Admin',
            'user_data' => $row,
            'table_row' => $table_row,
        ];
    
        $this->view("admin/products", $data);         
    }
}
