<?php

define('DS', DIRECTORY_SEPARATOR);
define('APP', dirname(dirname(__FILE__)) . DS);
define('ROOT', dirname(APP) . DS);
define('CONFIGS', APP . 'config' . DS);
define('CONTROLLERS', APP . 'controllers' . DS);
define('MODELS', APP . 'models' . DS);
define('VIEWS', APP . 'views' . DS);
define('LIBS', APP . 'libs' . DS);
define('CORE', APP . 'core' . DS);
define('TMP', APP . 'tmp' . DS);
define('CACHE', TMP . 'cache' . DS);
define('LOGS', TMP . 'logs' . DS);
define('WEB', APP . 'web' . DS);
define('PHP5', true);
define('DEFAULT_LANGUAGE', 'eng');