<?php
$router = new Router(); // create router instance 
 
$router->map('/', 
	array('controller' => 'home')
);
// Repositories
$router->map('/:project', 
	array('controller' => 'projects', 'action' => 'show', 'branch' => 'master'),
	array('project' => '[\w_-]+')
);
$router->map('/:project/tree',
	array('controller' => 'projects', 'action' => 'show', 'branch' => 'master'),
	array('project' => '[\w_-]+')
);
$router->map('/:project/tree/:branch',
	array('controller' => 'projects', 'action' => 'show'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+')
);

$router->map('/:project/branch_redirect',
	array('controller' => 'projects', 'action' => 'branch_redirect'),
	array('project' => '[\w_-]+')
);

$router->map('/:project/commit/:commit',
	array('controller' => 'commits', 'action' => 'show'),
	array('project' => '[\w_-]+', 'commit' => '[\w]+')
);


//Files
$router->map('/:project/blob/:branch/:filepath',
	array('controller' => 'files', 'action' => 'file'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+', 'filepath' => '[.]+')
);
$router->map('/:project/raw/:branch/:filepath',
	array('controller' => 'files', 'action' => 'raw'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+', 'filepath' => '[.]+')
);
$router->map('/:project/blame/:branch/:filepath',
	array('controller' => 'files', 'action' => 'blame'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+', 'filepath' => '[.]+')
);
$router->map('/:project/commits/:branch/:filepath',
	array('controller' => 'files', 'action' => 'commits'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+', 'filepath' => '[.]+')
);
$router->map('/:project/download/:branch/:filepath',
	array('controller' => 'files', 'action' => 'download'),
	array('project' => '[\w_-]+', 'branch' => '[\w_-]+', 'filepath' => '[.]+')
);
$router->default_routes();
$router->execute();