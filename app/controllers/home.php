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

        // Get featured items
        $product = $this->load_model('product');
        $featured_items = $product->featured_items();
        // show($featured_items);
        // die;
        
        // Data to send to view
        $data = [
            'page_tittle' => 'Home',
            'user_data' => $row,
            'featured_items' => $featured_items,
        ];
    
        $this->view("index", $data);         
    }
    
}


?>