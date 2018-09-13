<?php
require_once CORE . 'controller.php';
require_once LIBS . 'git.php';

class AppController extends Controller {

    protected function beforeFilter() {
        Git::loadRepositories();
        $title = 'Home';
        if (!empty($this->_request->params['project'])) {
          $title = $this->_request->params['project'];
        }
        System::set('title', $title);
    }

}
