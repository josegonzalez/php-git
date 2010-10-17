<?php
class Dispatcher {

    public static function dispatch($config, $request) {
        $controllerName = Inflector::classify("{$request->params['controller']}_controller");

        $controller = new $controllerName($config, $request);
        if (!is_object($controller)) diebug($request);

        $action = $request->params['action'];
        if (!method_exists($controller, $action)) diebug($request);

        // Call Action
        $controller->$action($request);
        $controller->render();
    }

}