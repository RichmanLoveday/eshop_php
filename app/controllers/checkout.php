<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Checkout extends Controller
{

    public function index()
    {
        $data = [];

        // Load models
        $product = $this->load_model('product');
        $countries = $this->load_model('Countries');
        $cart = $this->load_model('CartModel');
        $image_class = $this->load_model('Image');
        $user = $this->load_model('User');                // Load user model
        $checkout = $this->load_model('Orders');


        // check user login and fetch user session datas
        // check login
        $USER = (Auth::logged_in()) ? Auth::logged_in()  : false;
        if (!$USER) $this->redirect('login');

        $row1 = ($USER) ? $user->get_user_row($USER) : '';

        $prod_ids = [];
        $products = false;

        if (isset($_SESSION['CART'])) {
            $ids_str = array_column($_SESSION['CART'], 'id');
            $prod_ids = "'" . implode("','", $ids_str) . "'";
            $products = $cart->get_products($prod_ids);
        }

        // set sub total variable
        $sub_total = 0;

        // loop through products

        if (is_array($products)) {
            foreach ($products as $key => $row) {
                // resize image
                $products[$key]->image = $image_class->get_thumb_post($products[$key]->image);
                // loop through session carts and set cart quantity
                foreach ($_SESSION['CART'] as $item) {
                    if ($row->id == $item->id) {
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
        if (is_array($products)) rsort($products);

        // check if old data exist in session
        if (isset($_SESSION['POST_DATA'])) {
            $data['POST_DATA'] = $_SESSION['POST_DATA'];
        }


        // check for post variables 
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($_POST) > 0) {
            // check if user_id is ready or session_id
            $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : 0;
            $checkout->validate($_POST);        // validate data
            $data['errors'] = $checkout->errors;        // store errors

            // save post data in session to use in summary
            $_SESSION['POST_DATA'] = $_POST;
            $data['POST_DATA'] = $_POST;

            // redirect to summary page
            if (count($checkout->errors) == 0) {
                $this->redirect('checkout/summary');
            }
        }

        // show($data['POST_DATA']);
        // die;

        // Data to send to view
        $data['page_title'] = 'Checkout';
        $data['user_data'] = $row1;
        $data['sub_total'] = $sub_total;
        $data['countries'] = $countryList;
        $data['products'] = $products;
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
        
        $this->view("checkout", $data);
    }


    public function summary()
    {
        // load models
        $checkout = $this->load_model('Orders');
        $cart = $this->load_model('CartModel');
        $countries = $this->load_model('Countries');
        $user_m = $this->load_model('user');

        $data = [];

        // check login
        $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : false;
        if (!$user_id) $this->redirect('login');        // goto login

        // get products
        $prod_ids = [];
        $products = false;

        if (isset($_SESSION['CART'])) {
            $ids_str = array_column($_SESSION['CART'], 'id');
            $prod_ids = "'" . implode("','", $ids_str) . "'";
            $products = $cart->get_products($prod_ids);
        }

        // if cart is empty goto checkout
        if (!$products) $this->redirect('checkout');

        // set sub total variable
        $sub_total = 0;

        // loop through products
        if (is_array($products)) {
            foreach ($products as $key => $row) {
                // loop through session carts and set cart quantity
                foreach ($_SESSION['CART'] as $item) {
                    if ($row->id == $item->id) {
                        $products[$key]->cart_qty = $item->qty;
                        break;
                    }
                }
                // Add up sub total
                $sub_total += $row->price * $row->cart_qty;
            }
        }

        // products selected by customer and order details
        $data['order_details'] = $products;
        $data['orders'][] = $_SESSION['POST_DATA'];


        // check for post variables 
        // if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['POST_DATA'])) {
        //     // check if user_id is ready or session_id
        //     $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : 0;
        //     $session_id = session_id();

        //     $checkout->save_orders($_SESSION['POST_DATA'], $products, $user_id, $session_id, $countries);

        //     //  redirect to thank you page
        //     $this->redirect('checkout/thank_you');
        // }

        // show($products);
        // die;

        // show($data['orders']);
        // die;

        $data['user_data'] = $user_m->get_user_row($_SESSION['USER']);
        $data['description'] = "Order Number " . get_order_id();
        $data['page_title'] = 'Checkout Summary';
        $data['sub_total'] =  $sub_total;
        $data['ajax_url'] = ROOT . "payment";
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
        // $data['redirect_success'] = ROOT . "checkout/thank_you/success";
        // $data['redirect_error'] = ROOT . "checkout/thank_you/failed";

        $this->view('checkout.summary', $data);
    }

    public function thank_you($mode = 'success')
    {
        $data = [];

        // check login
        $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : false;
        if (!$user_id) $this->redirect('login');        // goto login

        // if cart is empty goto checkout
        if (empty($_SESSION['CART'])) $this->redirect('checkout');

        // clear post data information, cart and goto thank you page
        if ($mode == 'failed') {
            $data['page_title'] = 'Transaction failed';
            $this->view('checkout.transaction_failed', $data);
        }

        unset($_SESSION['POST_DATA']);
        unset($_SESSION['CART']);
        $data['page_title'] = 'Thank you';
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;

        $this->view('checkout.thank_you', $data);
    }
}