<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Profile extends Controller
{

    public function index($url_address = null)
    {
        // Load models
        $user = $this->load_model('User');
        $order = $this->load_model('Orders');

        $data = [];

        $USER = Auth::logged_in();
        //show($USER); die;
        if (!$USER) $this->redirect('login');         // Redirect user to login


        // if (!$user) $this->redirect('login');         // Redirect to login 

        // get user datas and users orders
        if ($url_address && Auth::access('admin')) {
            $user = $user->get_user_by_admin($url_address);
            $orders = $order->get_orders_by_user($url_address);
        } else {
            $user = $user->get_user_row($USER);
            $orders = $order->get_orders_by_user($USER->url_address);
        }


        // get orders details
        //show($orders); 
        if (is_array($orders)) {
            // get orders details
            foreach ($orders as $key => $row) {
                $orders[$key]->details = $order->get_order_details($row->id);
                $orders[$key]->user = $user;
                $orders[$key]->grand_total = number_format(array_sum(array_column($orders[$key]->details, 'total')));
            }
        }


        // Data to send to view
        $data = [
            'page_title' => 'Profile',
            'user_data' => $user,
            'orders' => $orders,
        ];

        $this->view("profile", $data);
    }
}