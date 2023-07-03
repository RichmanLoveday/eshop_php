<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Blog extends Controller
{
    public $limit = 2;
    public $offset;
    public function __construct()
    {
        // get page offset
        $this->offset = Pagination::get_offset($this->limit);
    }

    public function index()
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
            $blogs = $blog->get_all($find, $this->limit, $this->offset);
        } else {
            $blogs = $blog->get_all(null, $this->limit, $this->offset);
        }


        if ($blogs) {
            foreach ($blogs as $key => $item) {
                $blogs[$key]->name = $user->get_user_by_admin($blogs[$key]->user_url)->name;
                $blogs[$key]->image = ROOT . $image_class->get_thumb_blog_post($blogs[$key]->image);
            }
        }

        $data['page_title'] = 'Blogs';
        $data['user_data'] = $row;
        $data['show_search'] = true;
        $data['blogs'] = $blogs;
        $data['categories'] = $category->get_active_cat();
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;

        $this->view("blog", $data);
    }
}
