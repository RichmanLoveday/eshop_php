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
            $blogs[0]->name = $user->get_user_by_admin($blogs[0]->user_url)->name;
            $blogs[0]->image = ROOT . $image_class->get_thumb_blog_post($blogs[0]->image);
        }


        // Data to send to view

        $data['page_title'] = 'Post Unknown';
        $data['user_data'] = $row;
        $data['show_search'] = true;
        $data['blogs'] = $blogs[0];
        $data['categories'] = $category->get_active_cat();

        $this->view("single_post", $data);
    }
}
