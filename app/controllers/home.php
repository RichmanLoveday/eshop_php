<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Home extends Controller
{
    public $limit = 12;
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

        // check for searches
        $search = false;
        if (isset($_GET['find'])) {
            //   show($_GET);
            $search = true;
        }

        // $search = isset($_GET['search']) ? true : false;
        if (isset($_GET['search'])) {
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
        if ($search) {
            // if find request is seen
            if (isset($_GET['find'])) {
                $products_data = $product->featured_items(addslashes($_GET['find']), $this->limit, $this->offset);
            } else {
                // advance search
                $products_data = $product->get_product_by_search(Search::advance_search($_GET), $this->limit, $this->offset);
            }
        } else {
            $products_data = $product->featured_items(null, $this->limit, $this->offset);
        }

        // resize image
        if ($products_data) {
            foreach ($products_data as $key => $item) {
                $products_data[$key]->image = $image_class->get_thumb_post($products_data[$key]->image);
            }
        }

        $sliders = $slider->get_all();
        if ($sliders) {
            foreach ($sliders as $key => $item) {
                $sliders[$key]->image = $image_class->get_thumb_post($sliders[$key]->image, 484, 441);
            }
        }

        // recommended items carousel
        $data['sliders_rows'] = recommended_items_carousel($product, $image_class);

        // get all categories
        $data['categories'] = $category->get_active_cat();

        // get products for lower segment
        $data['segment_data'] = $this->get_segment_data($data['categories'], $product, $image_class);

        // show($data['sliders_rows']);
        // die;

        // Data to send to view
        $data['page_title'] = 'Home';
        $data['user_data'] = $row;
        $data['featured_items'] = $products_data;
        $data['sliders'] = $sliders;
        $data['total_cart'] = isset($_SESSION['CART']) ? count($_SESSION['CART']) : null;
        $data['show_search'] = true;

        $this->view("index", $data);
    }


    private function get_segment_data(array $categories, &$product, &$image_class): array
    {

        // display only 5 categories
        $mycats = [];
        $result = [];
        $num = 0;

        foreach ($categories as $cat) {
            // get thiers products
            $rows = $product->get_products_by_cat_id($cat->id, 'segment');
            if (is_array($rows)) {
                $cat->category = str_replace(" ", "_", $cat->category);
                $cat->category = preg_replace("/\W+/", "", $cat->category);

                // crop images
                foreach ($rows as $key => $row) {
                    $rows[$key]->image = $image_class->get_thumb_post($rows[$key]->image);
                }

                // add to catgories
                $result[$cat->category][] = $rows;
            }

            // break if it reaches five cats
            $num++;
            if ($num > 5) break;
        }

        return $result;
    }
}
