<?php

class HomeController extends Controller {

    public function index() {
        $this->_config['title'] = 'Home';
        $repos = $this->Project->findAll($this->_config);
        $this->set(compact('repos'));
    }

}