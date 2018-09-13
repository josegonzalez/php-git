<?php
include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

use Cake\Utility\Inflector;

include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'constants.php');
include_once(CORE . 'basics.php');
include_once(CORE . 'system.php');
include_once(CORE . 'router.php');
include_once(CONFIGS . 'core.php');
include_once(CONFIGS . 'routes.php');

System::verify($CONFIG);
System::load($CONFIG);
System::dispatch($router->request);
