<?php

class CommitsController extends Controller {

    function show() {
        Git::loadRepositories($this->_config);
        $this->_config['title'] = 'Commit' . $this->_request->params['commit'];
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