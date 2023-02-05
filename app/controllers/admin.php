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

        //  Load user data
        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        // Load categories 
        $category = $this->load_model('Category');
        $cats = $category->get_all_data('categories');

        // load category table
        if(is_array($cats)) $table_row = $category->make_table($cats);
        

        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin',
            'user_data' => $row,
            'table_row' => $table_row,
        ];
    
        $this->view("admin/categories", $data);         
    }


    public function products() {
        $data = [];
        
        $url = Auth::logged_in();
        if(!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        //  Load user data
        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        // Load products
        $products = $this->load_model('product');
        $products_data = $products->get_all_data('products');

        // Load categories
        $category = $this->load_model('Category');
        $category_data = $category->get_active_cat();

        // show($category_data);
        // die;
        // load products table
        if(is_array($products_data)) $table_row = $products->make_table($products_data);
        
        //show($table_row);
        if(!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin',
            'user_data' => $row,
            'categories' => (!$category_data) ? 'No category found' : $category_data,
            'table_row' => !empty($table_row) ? $table_row : 'No record found',
        ];
    
        $this->view("admin/products", $data);         
    }
}


?>