<?php

use app\core\Database;
use app\core\Models;

class Orders extends Models
{

    public function validate($post)
    {
        // validate data sent from post
        $this->errors = []; // reset errors

        foreach ($post as $key => $value) {
            if ($key === "country") {
                if (empty($value) || $value === "-- Select Country --") {
                    $this->errors['errCountry'] = 'Please enter a valid coiuntry';
                }
            }

            if ($key === "state") {
                if (empty($value) || $value === "-- State / Province / Region --") {
                    $this->errors['errState'] = 'Please enter a valid state';
                }
            }

            if ($key === "delivery_address") {
                if (empty($value)) {
                    $this->errors['errAddress'] = 'Enter a your address';
                }
            }

            if ($key === "address2") {
                if (empty($value)) {
                    $this->errors['errAddress2'] = 'Enter an additional address';
                }
            }

            if ($key === "phone") {
                if (empty($value)) {
                    $this->errors['errPhone'] = 'Please enter a phone number';
                }
            }

            if ($key === "home_phone") {
                if (empty($value)) {
                    $this->errors['errMobile_phone'] = 'Please enter a phone number';
                }
            }
        }
    }

    public function save_orders($post, $products, $user_url, $session_id, &$countryModel)
    {

        $DB = Database::newInstance();      // Database connection

        // get total form cart_qty * price
        $total = 0;
        foreach ($products as $item) {
            $total += $item->cart_qty * $item->price;
        }
        // echo $total;
        // show($products);
        // show($post); die;
        // store datas

        // show($products);

        if (is_array($products) && count($this->errors) === 0) {
            $data = [];
            $data['user_url'] = $user_url;
            $data['session_id'] = $session_id;
            $data['delivery_address'] =  $post['delivery_address'] . ' ' . $post['address2'];
            $data['zip'] = $post['zip'];
            // $country_obj = $countryModel->get_country($post['country']);
            // $state_obj = $countryModel->get_state($post['state']);
            $data['country'] = $post['country'];
            $data['state'] = $post['state'];
            $data['home_phone'] = $post['phone'];
            $data['mobile_phone'] = $post['mobile_phone'];
            $data['date'] = date('Y-m-d H:i:s');
            $data['shipping'] = 0;
            $data['total'] = $total;
            $data['tax'] = 0;
            $data['description'] = "Order Number " . get_order_id();
            //show($data); die;

            $query = "INSERT INTO orders (description, user_url, delivery_address, total, country, state, tax, zip, shipping, date, session_id, home_phone, mobile_phone) values (:description, :user_url, :delivery_address, :total, :country, :state, :tax, :zip, :shipping, :date, :session_id, :home_phone, :mobile_phone)";

            $result = $DB->write($query, $data);

            // save details
            // $query = "SELECT id FROM orders ORDER BY id DESC limit 1";
            // $result = $DB->read($query);

            // if (is_array($result)) {
            //     $orderid = $result[0]->id;
            // }

            foreach ($products as $item) {
                $data = [];
                $data['orderid'] = get_order_id();
                $data['qty'] = $item->cart_qty;
                $data['productid'] = $item->id;
                $data['description'] = $item->description;
                $data['amount'] = $item->price;
                $data['total'] = $item->cart_qty * $item->price;
                //show($data);
                $query = "INSERT INTO order_details (orderid, productid, qty, description, amount, total) values (:orderid, :productid, :qty, :description, :amount, :total)";
                $result = $DB->write($query, $data);
            }
            return $result ? true : false;
        }
    }


    public function get_orders_by_user($user_url)
    {
        $DB = Database::newInstance();      // Database connection

        $query = "SELECT * FROM orders WHERE user_url = :user_url ORDER BY id DESC limit 100";
        $orders = $DB->read($query, ['user_url' => $user_url]);

        // check if orders is array
        return (is_array($orders)) ? $orders : false;
    }


    public function get_all_orders($limit, $offsets, $find = null)
    {
        $DB = Database::newInstance();      // Database connection

        if (is_null($find)) {
            $query = "SELECT * FROM orders ORDER BY id DESC limit $limit offset $offsets";
            $orders = $DB->read($query);
        } else {
            $query = "SELECT * FROM orders ORDER BY id DESC limit $limit offset $offsets";
            $orders = $DB->read($query);
        }

        // check if orders is array
        return (is_array($orders)) ? $orders : false;
    }


    public function get_order_details($order_id)
    {
        $DB = Database::newInstance();      // Database connection

        $query = "SELECT * FROM order_details WHERE orderid = :order_id ORDER BY id DESC";
        $details = $DB->read($query, [':order_id' => $order_id]);

        // check if orders is array
        return (is_array($details)) ? $details : false;
    }

    public function payment($details, $user_id)
    {
        $DB = Database::newInstance();
        $data['trans_id'] = $details->transaction_id;
        $data['payer_id'] = $user_id;
        $data['first_name'] = $details->customer->name;
        $data['email'] = $details->customer->email;
        $data['amount'] = $details->amount;
        $data['date']  = $details->created_at;
        $data['order_id'] = "Order Number " . get_order_id();
        $data['raw'] = json_encode($details);
        $data['event_type'] = 'ORDER' . $details->charge_response_message;
        $data['status'] = $details->status;
        $data['summary'] = "On order has been approved by buyer";

        $query = "INSERT INTO payments (trans_id, summary, payer_id, first_name, email, amount, order_id, raw, event_type, status, date) values(:trans_id, :summary, :payer_id, :first_name, :email, :amount, :order_id, :raw, :event_type, :status, :date)";

        $result = $DB->write($query, $data);

        if ($result) return true;

        return false;
    }
}
