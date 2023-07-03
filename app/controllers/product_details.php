<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Product_details extends Controller
{

    public function index($slag)
    {
        $data = [];
        $slag = esc($slag);

        $product = $this->load_model('Product');
        $category = $this->load_model('Category');
        $user = $this->load_model('User');                // Load user model
        $image_class = $this->load_model('Image');

        $USER = Auth::logged_in();
        //show($USER); die;

        $row = ($USER) ? $user->get_user_row($USER) : '';

        $single_product = $product->single_product($slag);
        // show($single_product);
        // die;
        $image = [];
        if (is_object($single_product)) {
            $single_product->image = $image_class->get_thumb_post($single_product->image, 484, 441);

            // number of images
            $images = [
                $single_product->image,
                $single_product->image2,
                $single_product->image3,
                $single_product->image4,
            ];

            foreach ($images as $key => $value) {
                if ($value == "null" || empty($value)) continue;
                $image[] = $image_class->get_thumb_post($value, 484, 441);
            }
        }


        // Data to send to view
        $data = [
            'page_title' => 'Product Details',
            'user_data' => $row,
            'single_product' => $single_product,
            'categories' => $category->get_active_cat(),
            'images' => $image,
            'sliders_rows' => recommended_items_carousel($product, $image_class),    // recommended items carousel
            'total_cart' => isset($_SESSION['CART']) ? count($_SESSION['CART']) : null,
        ];

        $this->view("product-details", $data);
    }
}
