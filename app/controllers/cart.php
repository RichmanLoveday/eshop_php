<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Cart extends Controller
{

    public function index()
    {
        $data = [];
        $product = $this->load_model('product');
        $cart = $this->load_model('CartModel');
        $image_class = $this->load_model('Image');
        $user = $this->load_model('User');                // Load user model

        $USER = Auth::logged_in();
        //show($USER); die;

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

        if (is_array($products) && count($products) > 0) {
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

        // sort the products in asc order
        if (is_array($products) && count($products) > 0) rsort($products);

        // Data to send to view
        $data = [
            'page_title' => 'Cart',
            'user_data' => $row1,
            'sub_total' => number_format($sub_total, 2),
            'products' => is_array($products) ? $cart->make_table($products) : '',
            'total_cart' => isset($_SESSION['CART']) ? count($_SESSION['CART']) : null,
        ];

        $this->view("cart", $data);
    }
}