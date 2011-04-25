<?php
class CommitsController extends AppController {

    public function index() {
        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
        );

        $owner      = $this->Project->getOwner($this->_request->params['project']);
        $last_change= $this->Project->getLastChange($this->_request->params['project']);
        $description= $this->Project->getDescription($this->_request->params['project']);
        $tags       = $this->Project->getTags($this->_request->params['project']);
        $branches   = $this->Project->getBranches($this->_request->params['project']);
        $shortlogs  = $this->Project->getShortlog($this->_request->params['project']);
        $this->set(compact('owner', 'last_change', 'description', 'tags', 'branches', 'shortlogs'));
    }

    public function show() {
        System::set('title', 'Commit' . $this->_request->params['commit']);

        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
            'show'  => null,
            $this->_request->params['commit'] => sprintf("/%s/commit/%s",
                $this->_request->params['project'],
                $this->_request->params['commit']
            )
        );

        $tags       = $this->Project->getTags($this->_request->params['project']);
        $branches   = $this->Project->getBranches($this->_request->params['project']);
        $commit  = $this->Project->getCommit(
            $this->_request->params['project'],
            $this->_request->params['commit']
        );

        $diffs  = $this->Project->getDiff(
            $this->_request->params['project'],
            $this->_request->params['commit']
        );
        $this->set(compact('commit', 'diffs', 'branches', 'tags'));
    }

}