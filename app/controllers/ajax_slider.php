<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_slider extends Controller
{

    public function index()
    {
        // Collect data from axios or ajax
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);

        if (!(is_object($fetch))) {
            $fetch = (object) $_POST;
        }

        $slider = $this->load_model('slider');
        $image_class = $this->load_model('Image');

        $slider->create($fetch, $_FILES, $image_class);

        // check for error messages
        if (count($slider->errors) > 0) {
            echo 'Error found';
        } else {

            // get data and send as json
            $lastUpdateSlider = @end($slider->get_all());

            $data['id'] = $lastUpdateSlider->id;
            $data['header1_text'] = $lastUpdateSlider->header1_text;
            $data['header2_text'] = $lastUpdateSlider->header2_text;
            $data['text'] = $lastUpdateSlider->text;
            $data['link'] = $lastUpdateSlider->link;
            $data['image'] = ROOT . $lastUpdateSlider->image;
            $data['status'] = $lastUpdateSlider->disabled;
        }

        echo json_encode($data);
    }
}
