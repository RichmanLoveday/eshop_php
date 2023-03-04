<?php
use app\core\Database;
use app\core\Models;

Class Orders extends Models {
    
    public function save_orders($post, $products, $user_url, $session_id, &$countryModel) {

        // validate data sent from post
        foreach($post as $key => $value) {
            if($key === "country") {
                if(empty($value) || $value === "-- Select Country --") {
                    $this->errors['errCountry'] = 'Please enter a valid coiuntry';
                }
            }

            if($key === "state") {
                if(empty($value) || $value === "-- State / Province / Region --") {
                    $this->errors['errState'] = 'Please enter a valid state';
                }
            }
        }

        $DB = Database::newInstance();      // Database connection

        // get total form cart_qty * price
        $total = 0;
        foreach($products as $item) {
            $total += $item->cart_qty * $item->price;
        }
        // echo $total;
       // show($products);
        // show($post); die;
        // store datas

       // show($products);

        if(is_array($products) && count($this->errors) === 0) {
            $data = [];
            $data['user_url'] = $user_url;
            $data['session_id'] = $session_id;
            $data['delivery_address'] =  $post['delivery_address'] . ' ' . $post['address2'];
            $data['zip'] = $post['zip'];
            $country_obj = $countryModel->get_country($post['country']);
            $state_obj = $countryModel->get_state($post['state']);
            $data['country'] = $country_obj->country;
            $data['state'] = $state_obj->state;
            $data['home_phone'] = $post['phone'];
            $data['mobile_phone'] = $post['mobile_phone'];
            $data['date'] = date('Y-m-d H:i:s');
            $data['shipping'] = 0;
            $data['total'] = $total;
            $data['tax'] = 0;

           //show($data); die;

            $query = "INSERT INTO orders (user_url, delivery_address, total, country, state, tax, zip, shipping, date, session_id, home_phone, mobile_phone) values (:user_url, :delivery_address, :total, :country, :state, :tax, :zip, :shipping, :date, :session_id, :home_phone, :mobile_phone)";

            $result = $DB->write($query, $data);
            
            // save details
            
            $orderid = 0;
            $query = "SELECT id FROM orders ORDER BY id DESC limit 1";
            $result = $DB->read($query);

            if($result) {
                $orderid = $result[0]->id;
            }


            foreach($products as $item) {
                $data = [];
                $data['orderid'] = $orderid;
                $data['qty'] = $item->cart_qty;
                $data['productid'] = $item->id;
                $data['description'] = $item->description;
                $data['amount'] = $item->price;
                $data['total'] = $item->cart_qty * $item->price;
                //show($data);
                $query = "INSERT INTO order_details (orderid, productid, qty, description, amount, total) values (:orderid, :productid, :qty, :description, :amount, :total)";
                $result = $DB->write($query, $data);
            }
          
        }
    }


    public function get_orders_by_user($user_url) {
        $DB = Database::newInstance();      // Database connection
        
        $query = "SELECT * FROM orders WHERE user_url = :user_url ORDER BY id DESC limit 100";
        $orders = $DB->read($query, ['user_url' => $user_url]);
      
        // check if orders is array
        return (is_array($orders)) ? $orders : false;

    }


    public function get_all_orders() {
        $DB = Database::newInstance();      // Database connection
        
        $query = "SELECT * FROM orders ORDER BY id DESC limit 100";
        $orders = $DB->read($query);
      
        // check if orders is array
        return (is_array($orders)) ? $orders : false;

    }


    public function get_order_details($order_id) {
        $DB = Database::newInstance();      // Database connection
        
        $query = "SELECT * FROM order_details WHERE orderid = :order_id ORDER BY id DESC";
        $details = $DB->read($query, [':order_id' => $order_id]);
      
        // check if orders is array
        return (is_array($details)) ? $details : false;
    }
}


?>