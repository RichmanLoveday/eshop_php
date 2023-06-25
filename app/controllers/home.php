<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Home extends Controller
{
    public $limit = 10;
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
        $featured_items = $product->featured_items(null, $this->limit, $this->offset);

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

        // For carousel
        $carousel_val = 3;
        for ($i = 0; $i < $carousel_val; $i++) {
            $slider_row[$i] = $product->get_random_product();
            if ($slider_row[$i]) {
                foreach ($slider_row[$i] as $key => $rows) {
                    $slider_row[$i][$key]->image = $image_class->get_thumb_post($slider_row[$i][$key]->image);
                }
            }
            $data['sliders_rows'][] = $slider_row[$i];
        }


        // get all categories
        $data['categories'] = $category->get_active_cat();

        // get products for lower segment
        $data['segment_data'] = $this->get_segment_data($data['categories'], $product, $image_class);

        // show($data['sliders_rows']);
        // die;

        // Data to send to view
        $data['page_title'] = 'Home';
        $data['user_data'] = $row;
        $data['featured_items'] = $featured_items;
        $data['sliders'] = $sliders;
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