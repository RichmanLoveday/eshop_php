<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_checkout extends Controller {

    public function index($id = '') {
        // Collect data from axios or ajax
       

        $countries = $this->load_model('Countries');
        $countries = $countries->get_states($id);

        echo json_encode($countries);

    }
}


?>