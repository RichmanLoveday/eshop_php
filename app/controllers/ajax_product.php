<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_product extends Controller
{

    public $limit = 3;
    public $offset;
    public $page_num;
    public $fetch;

    public function __construct()
    {
        $this->fetch = file_get_contents("php://input");         // Get input files
        $this->fetch = json_decode($this->fetch);
        // check if type != get
        if (!(is_object($this->fetch))) {
            $this->fetch = (object) $_POST;
        }

        // get page offset
        $pagination = Pagination::get_offset($this->limit, $this->fetch->page_num ?? 1);
        $this->offset = $pagination[0];
        $this->page_num = $pagination[1];
    }

    public function index()
    {
        // Collect data from axios or ajax
        // $fetch = '';
        // $fetch =  file_get_contents("php://input");         // Get input files
        // $fetch = json_decode($fetch);


        // // check if type != get
        // if (!(is_object($fetch))) {
        //     $fetch = (object) $_POST;
        // }


        // show($_FILES);
        // show($fetch); die;
        $data = [];

        // Add product controller+
        if (is_object($this->fetch) && isset($this->fetch->data_type)) {
            $product = $this->load_model('product');
            $image_class = $this->load_model('Image');
            $brand = $this->load_model('brand');

            if ($this->fetch->data_type === 'add_product') {
                // add new product
                // load model
                $cats = $product->create($this->fetch, $_FILES, $image_class);

                if (!$cats) {
                    $data['message'] = $product->errors;
                    $data['message_type'] = 'error';
                    $data['data'] = '';
                    $data['data_type'] = "add_new";
                    echo json_encode($data);
                    // die;
                }

                if ($cats) {
                    $data = [];
                    $cats = $product->get_all_data('products', $this->limit, $this->offset);

                    // show($cats);
                    // die;
                    // Data to be sent to javascript
                    $data['data'] = $product->make_table($cats);
                    $data['message'] = $product->success_message;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "add_new";
                    // show($data);
                    // die;
                    echo json_encode($data);
                }
            }

            // Edit product
            if ($this->fetch->data_type === 'get_product_data') {
                $id = $this->fetch->id;
                $product_data = $product->get_single_data('products', $id);

                // get cat single data
                $cats = $product->get_single_data('categories', $product_data->category);

                // get brand single data
                $brands = $brand->get_single_data('brands', $product_data->brand);

                //show($product_data); die;
                if ($product_data) {
                    $data = [];

                    $data['id'] = $product_data->id;
                    $data['description'] = $product_data->description;
                    $data['category'] = $cats->category;
                    $data['brand'] = $brands->brand;
                    $data['price'] = $product_data->price;
                    $data['quantity'] =  $product_data->quantity;
                    $data['image'] =  $product_data->image;
                    $data['image2'] =  !empty($product_data->image2) ? $product_data->image2 : null;
                    $data['image3'] =  !empty($product_data->image3) ? $product_data->image3 : null;
                    $data['image4'] =  !empty($product_data->image4) ? $product_data->image4 : null;
                }
                echo json_encode($data);
            }

            if ($this->fetch->data_type === 'edit_product') {
                // add new product
                // load model
                // show($this->fetch);
                // die;
                $cats = $product->edit($this->fetch, $_FILES, $image_class);

                if (!$cats) {
                    $data['message'] = $product->errors;
                    $data['message_type'] = 'error';
                    $data['data'] = '';
                    $data['data_type'] = "edit_product";
                    echo json_encode($data);
                }

                if ($cats) {
                    $data = [];
                    $products = $product->get_all_products($this->limit, $this->offset);

                    // Data to be sent to javascript
                    $data['data'] = $product->make_table($products);
                    $data['message'] = $product->success_message;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "edit_product";

                    echo json_encode($data);
                }
            }


            if ($this->fetch->data_type === 'delete_product') {
                // add new product
                // load model

                $result = $product->delete($this->fetch->id);

                if (!$result) {
                    $data['message'] = $product->errors;
                    $data['message_type'] = 'error';
                    $data['data'] = '';
                    $data['data_type'] = "edit_product";
                    echo json_encode($data);
                }

                if ($result) {
                    $data = [];
                    $products = $product->get_all_data('products', $this->limit, $this->offset);

                    // Data to be sent to javascript
                    $data['data'] = $product->make_table($products);
                    $data['message'] = $product->success_message;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "delete_product";

                    echo json_encode($data);
                }
            }



            // // Edit product controller
            // if($data->data_type === 'edit_row') {


            //     // Data to be sent to javascript
            //     $data['message'] = "";
            //     $data['message_type'] = 'info';
            //     $data['data'] = "";
            //     $data['data_type'] = "edit_row";

            //     echo json_encode($data);
            // }

            // Delete product controller
            // if($data->data_type === 'delete_row') {

            //     $id = $data->id;
            //     $cat_name = $data->product;


            //     $product = $this->load_model('product');
            //     $delete = $product->delete($id, $cat_name);      // Update product

            //     // Data to be sent to javascript

            //     if($delete) {
            //         $cats = $product->get_all_data('products');        // Get all data
            //         $data = [];

            //         // Data to be sent to javascript
            //         $data['message'] = $product->success_message;
            //         $data['message_type'] = 'info';
            //         $data['data'] = $product->make_table($cats);
            //         $data['data_type'] = "delete_row";

            //         echo json_encode($data);
            //     }   
            // }


            // if($data->data_type === 'disable_row') {
            //     // show($data);
            //     // die;
            //     $id = $data->id;
            //     $state = $data->current_state;


            //     $product = $this->load_model('product');
            //     $update = $product->disable_row($id, $state);      // Update product

            //     // Data to be sent to javascript
            //     if($update) {
            //         $cats = $product->get_all_data('products');        // Get all data

            //         $data = [];
            //         $data['message'] = $product->success_message;
            //         $data['message_type'] = 'info';
            //         $data['data'] = $product->make_table($cats);
            //         $data['data_type'] = "disable_row";
            //         $data['current_state'] = $data->current_state;
            //     }


            //     echo json_encode($data);
            // }


            // if($data->data_type === 'get_cat_data') {
            //     $id = $data->id;

            //     $product = $this->load_model('product');
            //     $cats = $product->get_single_data('products', $id);

            //     if($cats) {
            //         $data = [];

            //         $data['current_state'] = $cats->disabled;
            //         $data['input'] = $cats->product;
            //         $data['id'] = $cats->id;
            //         $data['data_type'] = 'data_row';


            //     }
            //     echo json_encode($data);

            // }
        }
    }
}