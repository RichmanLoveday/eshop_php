<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Admin extends Controller
{

    public $limit = 3;
    public $offset;
    public $page_num;
    public function __construct()
    {
        // get page offset
        $this->offset = Pagination::get_offset($this->limit)[0];
        $this->page_num = Pagination::get_offset($this->limit)[1];
    }

    public function index()
    {
        $data = [];

        $url = Auth::logged_in();
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        if (!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin',
            'user_data' => $row,
        ];

        $this->view("admin/index", $data);
    }


    public function categories()
    {
        $data = [];

        $url = Auth::logged_in();
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        //  Load user data
        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        // Load categories 
        $category = $this->load_model('Category');
        $cats_all = $category->get_all_data('categories', $this->limit, $this->offset);
        $cats_some = $category->get_active_cat();

        // load category table
        if (is_array($cats_all)) $table_row = $category->make_table($cats_all);


        if (!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin - categories',
            'current_page' => 'categories',
            'user_data' => $row,
            'table_row' => $table_row ?? null,
            'categories' => $category->make_parent($cats_some),
        ];

        $this->view("admin/categories", $data);
    }


    public function products()
    {
        $data = [];

        $url = Auth::logged_in();
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        //  Load user data
        $user = $this->load_model('User');                // Load user model
        $row = $user->get_user_row($url);

        // Load products
        $products = $this->load_model('product');
        $products_data = $products->get_all_products($this->limit, $this->offset);

        // Load categories
        $category = $this->load_model('Category');
        $category_data = $category->get_active_cat();

        // load brands
        $brand = $this->load_model('Brand');
        $brand_data = $brand->get_active_brand();

        // show($category_data);
        // die;
        // load products table
        if (is_array($products_data)) $table_row = $products->make_table($products_data);

        //show($table_row);
        if (!$row) $this->redirect('login');         // Redirect to login   

        // Data to send to view
        $data = [
            'page_tittle' => 'Admin - Products',
            'current_page' => 'products',
            'user_data' => $row,
            'categories' => (!$category_data) ? 'No category found' : $category_data,
            'brands' => (!$brand_data) ? 'No brand found' : $brand_data,
            'table_row' => !empty($table_row) ? $table_row : 'No record found',
            'page_num' => $this->page_num,
        ];

        $this->view("admin/products", $data);
    }


    public function orders()
    {

        $order = $this->load_model('Orders');
        $user = $this->load_model('User');                // Load user model
        $url = Auth::logged_in();
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        //  Load user data
        $user_row = $user->get_user_row($url);


        // get orders details
        $orders = $order->get_all_orders($this->limit, $this->offset);

        if (is_array($orders)) {
            // get orders details
            foreach ($orders as $key => $row) {
                $orders[$key]->details = $order->get_order_details($row->id);
                $orders[$key]->grand_total = 0;

                if (is_array($orders[$key]->details)) {
                    $orders[$key]->grand_total = number_format(array_sum(array_column($orders[$key]->details, 'total')));
                }
                $orders[$key]->user = $user->get_user($row->user_url);
            }
        }


        // data sent to view
        $data = [
            'page_tittle' => 'Admin - Orders',
            'current_page' => 'orders',
            'user_data' => $user_row,
            'orders' => $orders,
        ];

        $this->view("admin/orders", $data);
    }

    public function users($role = 'customers')
    {

        // load models
        $user = $this->load_model('User');                // Load user model
        $order = $this->load_model('Orders');
        $url = Auth::logged_in();

        // check for login
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        // check for access

        //  Load admin data
        $user_row = $user->get_user_row($url);


        // read all data depending on roles
        $users = ($role == 'admins') ?  $user->get_admins() : $user->get_customers();

        // show($users);
        // die;
        // loop through and get users num of orders
        if (is_array($users)) {
            foreach ($users as $user) {
                $user->order_count = ($order->get_orders_by_user($user->url_address)) ? count($order->get_orders_by_user($user->url_address)) : 0;
            }
        }

        // data sent to view
        $data = [
            'page_tittle' => "Admin - $role",
            'current_page' => 'users',
            'users' => $users,
            'user_data' => $user_row,
        ];

        // show($users);
        // die;
        // die;
        // View
        ($role == 'admins') ? $this->view("admin/admins", $data) :
            $this->view("admin/users", $data);
    }


    public function settings(string $type = '')
    {
        // load models
        $user = $this->load_model('User');                // Load user model
        //$setting = $this->load_model('settings');
        $slider = $this->load_model('slider');
        $setting = new Settings();
        $url = Auth::logged_in();

        // check for login
        if (!$url || !Auth::access('admin')) $this->redirect('login');

        //  Load admin data
        $user_row = $user->get_user_row($url);

        // check for post variables
        // show($_POST);
        // die;

        if ($type == 'socials') {
            if (count($_POST) > 0) {
                $setting->save($_POST);
                $this->redirect('admin/settings/socials');
            }

            $data['settings'] = $setting->get_all();
            $data['type'] = 'socials';
        }

        if ($type == 'sliderImages') {
            $data['type'] = 'sliderImages';
            $data['slider_details'] = $slider->get_all();
        }

        // data sent to view
        $data['current_page'] = 'settings';
        $data['page_tittle'] = "Admin - $type";
        $data['user_data'] = $user_row;


        $this->view("admin/settings", $data);
    }

    public function messages($type = '')
    {

        // load models
        $user = $this->load_model('User');                // Load user model
        $order = $this->load_model('Orders');
        $Message = $this->load_model('message');
        $url = Auth::logged_in();

        // check for login
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        // check for access

        //  Load admin data
        $user_row = $user->get_user_row($url);

        // load messages
        $messeges = $Message->get_all();

        // data sent to view
        $data = [
            'page_tittle' => "Admin - messages",
            'current_page' => 'messages',
            'user_data' => $user_row,
            'messages' => $messeges,
        ];

        // show($users);
        // die;
        // die;
        // View

        $this->view("admin/messages", $data);
    }

    public function blogs($type = '')
    {

        // load models
        $user = $this->load_model('User');                // Load user model
        $Blog = $this->load_model('Blogs');
        $image_class = $this->load_model('image');
        $url = Auth::logged_in();

        // check for login
        if (!$url || !Auth::access('admin')) $this->redirect('login');         // Redirect user to home

        // check for access

        //  Load admin data
        $user_row = $user->get_user_row($url);

        // load messages
        $blogs = $Blog->get_all(null, $this->limit, $this->offset);


        // get user data
        if (isset($blogs) && is_array($blogs)) {
            foreach ($blogs as $key => $value) {
                if (file_exists($blogs[$key]->image)) {
                    $blogs[$key]->image = $image_class->get_thumb_post($blogs[$key]->image);
                }

                // user data
                $blogs[$key]->user_data = $user->get_user($blogs[$key]->user_url);
            }
        }
        // show($messeges);
        // die;
        // data sent to view
        $data = [
            'page_tittle' => "Admin - blogs",
            'current_page' => 'messages',
            'user_data' => $user_row,
            'current_page' => 'blogs',
            'blogs' => $blogs,
        ];

        // show($users);
        // die;
        // die;
        // View

        $this->view("admin/blogs", $data);
    }
}
