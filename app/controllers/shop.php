<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Shop extends Controller
{
    use Settings;
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
            $featured_items = $product->featured_items(addslashes($_GET['find']));
        } else {
            $featured_items = $product->featured_items();
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
            'SETTINGS' => $this->get_all_setting_as_object(),
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
        $data['SETTINGS'] = $this->get_all_setting_as_object();
        $data['featured_items'] = $featured_items;
        $data['categories'] = $category->get_active_cat();

        $this->view("shop", $data);
    }
}