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


        $Blog = $this->load_model('blog');
        $image_class = $this->load_model('Image');

        $Blog->create($fetch, $_FILES, $image_class);

        // check for error messages
        if (count($Blog->errors) > 0) {
            echo 'Error found';
        } else {

            // get data and send as json
            $lastUpdatePost = @end($Blog->get_all());

            // show($lastUpdatePost);
            // die;

            $data['id'] = $lastUpdatePost->id;
            $data['url'] = ROOT;
            $data['title'] = $lastUpdatePost->title;
            $data['owner'] = $lastUpdatePost->user_url;
            $data['post'] = $lastUpdatePost->post;
            $data['image'] = ROOT . $lastUpdatePost->image;
            $data['message'] = $Blog->success_message;
            $data['date'] = date("jS M Y H:i a", strtotime($lastUpdatePost->date));
            $data['error'] = false;
            // $data['status'] = $lastUpdatePost->disabled;
        }

        echo json_encode($data);
    }
}
