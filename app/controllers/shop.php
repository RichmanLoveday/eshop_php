<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Shop extends Controller
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
        $search = false;
        if (isset($_GET['find'])) {
            $search = true;
        }

        $data = [];
        $product = $this->load_model('product');
        $image_class = $this->load_model('Image');
        $user = $this->load_model('User');                // Load user model
        $category = $this->load_model('category');

        $USER = Auth::logged_in();
        //show($USER); die;

        $row = ($USER) ? $user->get_user_row($USER) : '';

        // Get featured items
        if ($search) {
            $featured_items = $product->featured_items(addslashes($_GET['find']), $this->limit, $this->offset);
        } else {
            $featured_items = $product->featured_items(null, $this->limit, $this->offset);
        }

        // resize image
        if ($featured_items) {
            foreach ($featured_items as $key => $item) {
                $featured_items[$key]->image = $image_class->get_thumb_post($featured_items[$key]->image);
            }
        }

        // show($featured_items);
        // die;

        // Data to send to view
        $data = [
            'page_title' => 'Shop',
            'user_data' => $row,
            'featured_items' => $featured_items,
            'categories' => $category->get_active_cat(),
            'show_search' => true,
        ];

        $this->view("shop", $data);
    }

    public function category($cat_find = null)
    {
        $data = [];
        $product = $this->load_model('product');
        $image_class = $this->load_model('Image');
        $user = $this->load_model('User');                // Load user model
        $category = $this->load_model('category');

        $USER = Auth::logged_in();
        //show($USER); die;

        $row = ($USER) ? $user->get_user_row($USER) : '';

        // get category by name
        $check = null;
        $check = $category->get_cat_by_name($cat_find);

        // check for retun value
        $check = is_object($check) ? $check : null;

        // Get product based on category id
        $featured_items = $product->get_products_by_cat_id($check->id);

        // resize image
        if ($featured_items) {
            foreach ($featured_items as $key => $item) {
                $featured_items[$key]->image = $image_class->get_thumb_post($featured_items[$key]->image);
            }
        }


        $data['page_title'] = 'Shop';
        $data['featured_items'] = $featured_items;
        $data['categories'] = $category->get_active_cat();

        $this->view("shop", $data);
    }
}
