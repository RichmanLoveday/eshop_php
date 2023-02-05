<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_category extends Controller {

    public function index() {
        // Collect data from axios or ajax
        $fetch =  file_get_contents("php://input");
        $fetch = json_decode($fetch);
        // show($fetch);
        $data = [];
        // show($fetch);

        // Add category controller+
        if(is_object($fetch) && isset($fetch->data_type)) {
            
            if($fetch->data_type === 'add_category') {
                // add new category
                // load model
                $category = $this->load_model('Category');
                $cats = $category->create($fetch);

                if(!$cats) {
                    $data['message'] = $category->errors;
                    $data['message_type'] = 'error';
                    $data['data'] = '';
                    $data['data_type'] = "add_new";
                    echo json_encode($data);
                }
                
                if($cats) {
                    $data = [];
                    $cats = $category->get_all_data('categories');
                    
                    // Data to be sent to javascript
                    $data['data'] = $category->make_table($cats);
                    $data['message'] = $category->success_message;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "add_new";
                    
                    echo json_encode($data);
                }
            }

            // Edit category
            if($fetch->data_type === 'edit_category') {
                // add new category
                // load model
                // show($fetch);
                
                $category = $this->load_model('Category');
                $cats = $category->edit($fetch->id, $fetch->data);

                if(!$cats) {
                    $data['message'] = $category->errors;
                    $data['message_type'] = 'error';
                    $data['data'] = '';
                    $data['data_type'] = "edit_cat";
                    echo json_encode($data);
                }
                
                if($cats) {
                    $data = [];
                    $cats = $category->get_all_data('categories');
                    
                    // Data to be sent to javascript
                    $data['data'] = $category->make_table($cats);
                    $data['message'] = $category->success_message;
                    $data['message_type'] = 'info';
                    $data['data_type'] = "edit_cat";
            
                    echo json_encode($data);
                }
            }


            // // Edit category controller
            // if($fetch->data_type === 'edit_row') {
                

            //     // Data to be sent to javascript
            //     $data['message'] = "";
            //     $data['message_type'] = 'info';
            //     $data['data'] = "";
            //     $data['data_type'] = "edit_row";

            //     echo json_encode($data);
            // }

            // Delete category controller
            if($fetch->data_type === 'delete_row') {
            
                $id = $fetch->id;
                $cat_name = $fetch->category;
                

                $category = $this->load_model('Category');
                $delete = $category->delete($id, $cat_name);      // Update category

                // Data to be sent to javascript

                if($delete) {
                    $cats = $category->get_all_data('categories');        // Get all data
                    $data = [];
                    
                    // Data to be sent to javascript
                    $data['message'] = $category->success_message;
                    $data['message_type'] = 'info';
                    $data['data'] = $category->make_table($cats);
                    $data['data_type'] = "delete_row";

                    echo json_encode($data);
                }   
            }


            if($fetch->data_type === 'disable_row') {
                // show($fetch);
                // die;
                $id = $fetch->id;
                $state = $fetch->current_state;
                
                
                $category = $this->load_model('Category');
                $update = $category->disable_row($id, $state);      // Update category

                // Data to be sent to javascript
                if($update) {
                    $cats = $category->get_all_data('categories');        // Get all data

                    $data = [];
                    $data['message'] = $category->success_message;
                    $data['message_type'] = 'info';
                    $data['data'] = $category->make_table($cats);
                    $data['data_type'] = "disable_row";
                    $data['current_state'] = $fetch->current_state;
                }
               

                echo json_encode($data);
            }


            if($fetch->data_type === 'get_cat_data') {
                $id = $fetch->id;

                $category = $this->load_model('Category');
                $cats = $category->get_single_data('categories', $id);

                if($cats) {
                    $data = [];

                    $data['current_state'] = $cats->disabled;
                    $data['input'] = $cats->category;
                    $data['id'] = $cats->id;
                    $data['data_type'] = 'data_row';
                    

                }
                echo json_encode($data);

            }
        }   

    }
}


?>