<?php

class ProjectsController extends Controller {

    function show() {
        Git::loadRepositories($this->_config);

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