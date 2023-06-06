<?php

use app\core\Controller;
use app\models\Auth;
use app\models\User;

class Ajax_message extends Controller
{

    public function index()
    {
        // Collect data from axios or ajax
        $fetch = file_get_contents("php://input");
        $fetch = json_decode($fetch);
        $data = [];

        // load models
        $message = $this->load_model('Message');
        // Add cart category
        if (is_object($fetch)) {
            $rmMsg = $message->delete_message($fetch->id);

            if ($rmMsg) {
                echo json_encode(['message' => $message->success_message, 'success' => true]);
            }
        }
    }
}
