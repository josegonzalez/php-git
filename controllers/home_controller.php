<?php
require_once CONTROLLERS . 'app_controller.php';

class HomeController extends AppController {

    public function index() {
        System::set('title', 'Home');
        $repos = $this->Project->findAll();
        $this->set(compact('repos'));
    }

}
