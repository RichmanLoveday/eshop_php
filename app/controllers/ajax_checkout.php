<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_checkout extends Controller {

    public function index() {
        // Collect data from axios or ajax
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);
        
        $countries = $this->load_model('Countries');
        $countries = $countries->get_states($fetch->id);
        
        $data = (object) [
            'data' => $countries,
            'data_type' => 'get_states',
        ];
    
        echo json_encode($data);

    }
}


?>