<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Home extends Controller
{

    public function index()
    {

        // check for searches
        $search = false;
        if (isset($_GET['find'])) {
            $search = true;
        }

        $product = $this->load_model('product');
        $image_class = $this->load_model('Image');
        $slider = $this->load_model('slider');
        $user = $this->load_model('User');                // Load user model
        $category = $this->load_model('category');

        $data = [];

        $USER = Auth::logged_in();
        //show($USER); die;

        $row = ($USER) ? $user->get_user_row($USER) : '';

        // Get featured items
        $featured_items = $product->featured_items();

        // resize image
        if ($featured_items) {
            foreach ($featured_items as $key => $item) {
                $featured_items[$key]->image = $image_class->get_thumb_post($featured_items[$key]->image);
            }
        }

        $sliders = $slider->get_all();
        if ($sliders) {
            foreach ($sliders as $key => $item) {
                $sliders[$key]->image = $image_class->get_thumb_post($sliders[$key]->image, 484, 441);
            }
        }

        // show($featured_items);
        // die;

        // show($slider->get_all());
        // die;
        // Data to send to view
        $data = [
            'page_title' => 'Home',
            //'SETTINGS' => $this->get_all_setting_as_object(),
            'user_data' => $row,
            'featured_items' => $featured_items,
            'categories' => $category->get_active_cat(),
            'sliders' => $sliders,
            'show_search' => true,
        ];

        $this->view("index", $data);
    }
}
