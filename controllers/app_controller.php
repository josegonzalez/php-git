<?php
class AppController extends Controller {

    protected function beforeFilter() {
        Git::loadRepositories();
        System::set('title', $this->_request->params['project']);
    }

}