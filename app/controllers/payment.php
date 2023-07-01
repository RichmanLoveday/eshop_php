<?php

use app\core\Controller;
use app\models\Auth;

class Payment extends Controller
{
    public function index()
    {

        $checkout = $this->load_model('Orders');
        $countries = $this->load_model('Countries');
        $product = $this->load_model('product');
        $cart = $this->load_model('CartModel');

        $data = file_get_contents('php://input');
        // $filename = time() . '_.txt';
        // file_put_contents($filename, $data);
        $data = json_decode($data);
        // show($data);
        // die;

        // check for post variables 
        if (isset($_SESSION['POST_DATA']) && $data->status == "successful") {
            $ids_str = array_column($_SESSION['CART'], 'id');
            $prod_ids = "'" . implode("','", $ids_str) . "'";
            $products = $cart->get_products($prod_ids);

            // check if user_id is ready or session_id
            $user_id = (Auth::logged_in()) ? Auth::logged_in()->url_address : 0;
            $session_id = session_id();

            $sub_total = 0;
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

            // save orders
            $checkout->save_orders($_SESSION['POST_DATA'], $products, $user_id, $session_id, $countries);


            // save payments
            $payment = $checkout->payment($data, $user_id);
            if ($payment) {
                $data = [];
                $data = [
                    'status' => true,
                    'redirect_success' => ROOT . "checkout/thank_you/success",
                ];

                echo json_encode($data);
            }

            //  redirect to thank you page
            // $this->redirect('checkout/thank_you');
        }
    }
}
