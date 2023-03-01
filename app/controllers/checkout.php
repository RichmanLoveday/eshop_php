<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Checkout extends Controller {

    public function index() {
        $data = [];

        // Load models
        $product = $this->load_model('product');
        $countries = $this->load_model('Countries');
        $cart = $this->load_model('CartModel');
        $image_class = $this->load_model('Image');
        $user = $this->load_model('User');                // Load user model
        $checkout = $this->load_model('Orders');
        

        // check user login and fetch user session datas
        $USER = Auth::logged_in();
        //show($USER); die;
         
        $row1 = ($USER) ? $user->get_user_row($USER) : '';

        $prod_ids = [];
        $products = false;
        
        if(isset($_SESSION['CART'])) {
            $ids_str = array_column($_SESSION['CART'], 'id');
            $prod_ids = "'". implode("','", $ids_str) . "'";
            $products = $cart->get_products($prod_ids);
        }
    
        // set sub total variable
        $sub_total = 0;
        
        // loop through products
        
        if(is_array($products)) {
            foreach($products as $key => $row) {
                // resize image
                $products[$key]->image = $image_class->get_thumb_post($products[$key]->image);
                // loop through session carts and set cart quantity
                foreach($_SESSION['CART'] as $item) {
                   if($row->id == $item->id) {
                    $products[$key]->cart_qty = $item->qty;
                    break;
                   }
                }
                // Add up sub total
                $sub_total += $row->price * $row->cart_qty;
            }
        }


        // get countries 
        $countryData = $countries->get_countries();
        $countryList = ($countries) ? $countries->make_countries($countryData) : '';     

        // sort the products and countries in asc order 
        if(is_array($products)) rsort($products);


        // check for post variables 
        if($_SERVER['REQUEST_METHOD'] === 'POST' && count($_POST) > 0) {
            // show($_POST);
            // show($products);
            // show($_SESSION);
            // echo $USER;

            // check if user_id is ready or session_id
            $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : 0;
            $session_id = session_id();

            $result = $checkout->save_orders($_POST, $products, $user_id, $session_id, $countries);
            
        }
         
        // Data to send to view
        $data = [
            'page_title' => 'Cart',
            'user_data' => $row1,
            'sub_total' => number_format($sub_total, 2),
            'products' => $products,
            'products_table' => $cart->make_table($products),
            'countries' => $countryList,
            'errors' => $checkout->errors,
        ];
    
        $this->view("checkout", $data);         
    }
    
}

?>