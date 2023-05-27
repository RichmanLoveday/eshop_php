<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Shop extends Controller
{

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
            'user_data' => $row,
            'featured_items' => $featured_items,
            'categories' => $category->get_active_cat(),
            'show_search' => true,
        ];

        $this->view("shop", $data);
    }

    public function category($category = null)
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


        $data['page_title'] = 'Shop';
        $this->view("shop", $data);
    }
}