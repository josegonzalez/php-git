<?php
class ProjectsController extends AppController {

    public function show() {
        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
            $this->_request->params['branch'] => "/{$this->_request->params['project']}/{$this->_request->params['branch']}",
        );
        if (!empty($this->_request->params['filepath'])) {
            $paths = explode('/', $this->_request->params['filepath']);
            $prevPath = "/{$this->_request->params['project']}/{$this->_request->params['branch']}";
            foreach ($paths as $i => $path) {
                $prevPath = $prevPath . '/' . $path;
                $this->_breadcrumbs[$path] = $prevPath;
            }
        }

        $tree = $this->Project->getTree(
            $this->_request->params['project'],
            $this->_request->params['filepath']
        );
        $this->set(compact('tree'));
    }

    public function blob() {
        $this->_breadcrumbs = array(
            'home' => '/',
            $this->_request->params['project'] => "/{$this->_request->params['project']}",
            $this->_request->params['branch'] => "/{$this->_request->params['project']}/tree/{$this->_request->params['branch']}",
        );

        if (!empty($this->_request->params['filepath'])) {
            $paths = explode('/', $this->_request->params['filepath']);
            $prevPath = "/{$this->_request->params['project']}/{$this->_request->params['branch']}";
            foreach ($paths as $i => $path) {
                $prevPath = $prevPath . '/' . $path;
                $this->_breadcrumbs[$path] = $prevPath;
            }
        }

        $data = $this->Project->getBlob(
            $this->_request->params['project'],
            $this->_request->params['filepath']
        );
        $this->set(compact('data'));
    }

}