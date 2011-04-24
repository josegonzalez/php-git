<?php

class HomeController extends Controller {

    public function index() {
        System::set('title', 'Home');
        $repos = $this->Project->findAll();
        $this->set(compact('repos'));
    }

}