<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Post extends Controller
{

    public function index($url_address = '')
    {


        // check for searches
        $search = false;
        if (isset($_GET['find'])) {
            $search = true;
        }

        $image_class = $this->load_model('Image');
        $blog = $this->load_model('blogs');
        $user = $this->load_model('User');
        $category = $this->load_model('category');

        $USER = Auth::logged_in();
        //show($USER); die;

        $row = ($USER) ? $user->get_user_row($USER) : '';

        if ($search) {
            $find = '%' . $_GET['find'] . '%';
            $blogs = $blog->get_all($find);
        } else {
            $blogs = $blog->get_single_blog($url_address);
        }

        if ($blogs) {
            $blogs[0]->image = ROOT . $image_class->get_thumb_blog_post($blogs[0]->image);
        }


        // show($slider->get_all());
        // die;
        // Data to send to view

        $data['page_title'] = 'Post Unknown';
        $data['user_data'] = $row;
        $data['show_search'] = true;
        $data['blogs'] = $blogs;
        $data['categories'] = $category->get_active_cat();
        // $data = [
        //     'page_title' => 'Home',
        //     //'SETTINGS' => $this->get_all_setting_as_object(),
        //     'user_data' => $row,
        //     'featured_items' => $featured_items,
        //     'categories' => $category->get_active_cat(),
        //     'sliders' => $sliders,
        //     'show_search' => true,
        // ];

        $this->view("single_blog", $data);
    }
}
