<?php

class ProjectsController extends Controller {

    function show() {
        Git::loadRepositories($this->_config);

        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
        );

        $this->_config['title'] = $this->_request->params['project'];
        $owner      = $this->Project->getOwner($this->_request->params['project']);
        $last_change= $this->Project->getLastChange($this->_request->params['project']);
        $description= $this->Project->getDescription($this->_request->params['project']);
        $tags       = $this->Project->getTags($this->_request->params['project']);
        $branches   = $this->Project->getBranches($this->_request->params['project']);
        $shortlogs  = $this->Project->getShortlog($this->_request->params['project']);
        $this->set(compact('owner', 'last_change', 'description', 'tags', 'branches', 'shortlogs'));
    }
}