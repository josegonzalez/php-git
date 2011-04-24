<?php

class ProjectsController extends Controller {

    protected function beforeFilter() {
        Git::loadRepositories($this->_config);
    }

    public function show() {
        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
        );

        $this->_config['title'] = $this->_request->params['project'];
        $tree = $this->Project->getTree(
            $this->_request->params['project'],
            $this->_request->params['filepath']
        );
        $this->set(compact('tree'));
    }
}