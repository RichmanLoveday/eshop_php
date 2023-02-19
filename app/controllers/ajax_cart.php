<?php use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_cart extends Controller {

    public function index() {
        // Collect data from axios or ajax
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);
        $data = [];
       // show($fetch); die;

        // load models
        $cart = $this->load_model('CartModel');
        $image_class = $this->load_model('Image');

        // Add cart category
        if(is_object($fetch) && isset($fetch->data_type)) {

            if($fetch->data_type === 'add_to_cart') {
                //show($fetch);
                // add new category
                // load model
                $product = $cart->get_one_data('products', 'id', $fetch->id);

                if(!$product) {
                    $data['message']="Unable to add to cart";
                    $data['message_type']='error';
                    $data['data']='';
                    $data['data_type']="add_to_cart";
                    echo json_encode($data);
                }

                if($product) {
                    //show($product); 
                    // add to cart
                    $arr = [ 
                    'id' => $product->id,
                    'qty'=> 1,
                    ];

                    $add_cart = $cart->add_to_cart('CART', (object) $arr);

                    if($add_cart) {
                        // data to be sent back to ajax
                        $data['message'] = $cart->success_message;
                        $data['message_type'] = 'info';
                        $data['data_type'] = "add_to_cart";

                    }


                    // $data = [];
                    // $cats = $category->get_all_data('categories');

                    // // Data to be sent to javascript
                    // $data['data'] = $category->make_table($cats);
                    // $data['message'] = $category->success_message;
                    // $data['message_type'] = 'info';
                    // $data['data_type'] = "add_new";

                    echo json_encode($data);
                }
            }

            if($fetch->data_type === 'increase_quantity') {
                //show($fetch);
                $id = esc($fetch->id);
                $inc_qty = $cart->increase_quantity('CART', $id, $image_class);
                if($inc_qty) {
                    // update table row
                    // show($inc_qty);
                    $data['products'] = $inc_qty;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "increase_quantity";
                    echo json_encode($data);
                }
            }


            if($fetch->data_type === 'decrease_quantity') {
                //show($fetch);
                $id = esc($fetch->id);
                $dcr_qty = $cart->decrease_quantity('CART', $id, $image_class);
                if($dcr_qty) {
                    // show($dcr_qty);
                    $data['products'] = $dcr_qty;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "decrease_quantity";
                    echo json_encode($data);
                }
            }

            if($fetch->data_type === 'remove_cart') {
                $id = esc($fetch->id);
                $rvm_cart = $cart->remove_cart('CART', $id, $image_class);
                if($rvm_cart) {
                    // show($dcr_qty);
                    $data['products'] = $rvm_cart;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "remove_cart";
                    echo json_encode($data);
                }
            }

            if($fetch->data_type === 'edit_quantity') {
               // show($fetch); die;
                $id = esc($fetch->id);
                $qty = esc($fetch->data);
                $edit_qty = $cart->edit_quantity('CART', $id, $qty,$image_class);

                if($edit_qty) {
                    $data['products'] = $edit_qty;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "edit_quantity";
                    echo json_encode($data);
                }

            }


        }

    }
}


?>