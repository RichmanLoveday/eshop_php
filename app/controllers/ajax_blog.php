<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_blog extends Controller
{

    public function index()
    {
        // Collect data from axios or ajax
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);

        if (!(is_object($fetch))) {
            $fetch = (object) $_POST;
        }


        $Blog = $this->load_model('blogs');
        $user = $this->load_model('User');
        $image_class = $this->load_model('Image');

        $Blog->create($fetch, $_FILES, $image_class);

        // check for error messages
        if (count($Blog->errors) > 0) {
            echo 'Error found';
        } else {

            // get data and send as json
            $lastUpdatePost = @end($Blog->get_all());

            $data['id'] = $lastUpdatePost->id;
            $data['url'] = ROOT;
            $data['title'] = $lastUpdatePost->title;
            $data['owner'] = $user->get_user($lastUpdatePost->user_url);
            $data['post'] = $lastUpdatePost->post;
            $data['image'] = ROOT . $image_class->get_thumb_post($lastUpdatePost->image);
            $data['message'] = $Blog->success_message;
            $data['date'] = date("jS M Y H:i a", strtotime($lastUpdatePost->date));
            $data['status'] = false;
            // $data['status'] = $lastUpdatePost->disabled;
        }

        echo json_encode($data);
    }


    public function edit()
    {
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);

        if (!(is_object($fetch))) {
            $fetch = (object) $_POST;
        }

        //show($fetch);
        // load model
        $Blog = $this->load_model('blogs');
        $image_class = $this->load_model('image');
        $user = $this->load_model('user');

        if ($fetch->type == 'get_data') {
            // get data 
            $single_blog = $Blog->get_single_data('blogs', $fetch->id);

            if ($single_blog) {
                $data['title'] = $single_blog->title;
                $data['post'] = $single_blog->post;
                $data['image'] = ROOT . $single_blog->image;
                $data['status'] = true;
            } else {
                $data['status'] = false;
            }


            echo json_encode($data);
        }

        if ($fetch->type == "edit_blog") {
            $result = "";
            if (isset($fetch->image)) {
                $result = $Blog->edit($fetch);
            } else {
                $result = $Blog->edit($fetch, $_FILES, $image_class);
            }

            // check for result
            if ($result) {
                // get result of updated blog
                $updated = $Blog->get_single_data('blogs', $fetch->id);

                $data['id'] = $updated->id;
                $data['title'] = $updated->title;
                $data['url'] = ROOT;
                $data['owner'] = $user->get_user($updated->user_url)->name;
                $data['post'] = $updated->post;
                $data['image'] = ROOT . $image_class->get_thumb_post($updated->image);
                $data['date'] = date("jS M Y H:i a", strtotime($updated->date));
                $data['message'] = $Blog->success_message;
                $data['status'] = false;
            }

            echo json_encode($data);
        }
    }


    public function delete()
    {
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);

        $Blog = $this->load_model('blogs');

        $deleted = $Blog->delete($fetch->id);

        if ($deleted) {
            $data['message'] =  $Blog->success_message;
            $data['status'] = true;
        }
        echo json_encode($data);
    }
}