<?php
include_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'constants.php');
include_once(CORE . 'basics.php');
include_once(CORE . 'router.php');
include_once(CORE . 'inflector.php');
include_once(CONFIGS . 'core.php');
include_once(CONFIGS . 'routes.php');

function __autoload($name) {
    if (file_exists(APP . Inflector::underscore($name)  . '.php')) {
        return require_once(APP . Inflector::underscore($name)  . '.php');
    }
    else if (file_exists(CONTROLLERS . Inflector::underscore($name)  . '.php')) {
        return require_once(CONTROLLERS . Inflector::underscore($name)  . '.php');
    }
    else if (file_exists(LIBS . Inflector::underscore($name)  . '.php')) {
        return require_once(LIBS . Inflector::underscore($name)  . '.php');
    }
    else {
        return require_once(CORE . Inflector::underscore($name)  . '.php');
    }
}

Verify::system($CONFIG);
Dispatcher::dispatch($CONFIG, $router->request);