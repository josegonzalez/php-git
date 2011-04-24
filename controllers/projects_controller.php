<?php

class ProjectsController extends Controller {

    protected function beforeFilter() {
        Git::loadRepositories();
    }

    public function show() {
        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
        );

        System::set('title', $this->_request->params['project']);
        $tree = $this->Project->getTree(
            $this->_request->params['project'],
            $this->_request->params['filepath']
        );
        $this->set(compact('tree'));
    }
}