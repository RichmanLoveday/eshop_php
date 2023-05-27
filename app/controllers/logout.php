<?php
use app\core\Controller;
use app\models\Auth;
use app\models\User;

Class Logout extends Controller {

    public function index() {
        Auth::logout();  
        $this->redirect('home');      
    }
}


?>