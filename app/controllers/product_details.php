<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Product_details extends Controller {

    public function index($slag) {
        $data = [];
        $slag = esc($slag);

        $product = $this->load_model('Product'); 
        $user = $this->load_model('User');                // Load user model
        $image_class = $this->load_model('Image');
        
        $USER = Auth::logged_in();
        //show($USER); die;
         
        $row = ($USER) ? $user->get_user_row($USER) : '';
        
        $single_product = $product->single_product($slag);
        // show($featured_items);
        // die;
        
        // Data to send to view
        $data = [
            'page_title' => 'Product Details',
            'user_data' => $row,
            'single_product' => $single_product,
            'images' => [$single_product ? $single_product->image : '', $single_product ? $single_product->image2 : '', $single_product ? $single_product->image3 : '', $single_product ? $single_product->image4 : ''],
        ];
    
        $this->view("product-details", $data);         
    }
    
}


?>