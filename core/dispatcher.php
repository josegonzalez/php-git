<?php
class Dispatcher {

    public static function dispatch($request) {
        $controllerName = Inflector::classify("{$request->params['controller']}_controller");

        $controller = new $controllerName($request);
        if (!is_object($controller)) diebug($request);

        $action = $request->params['action'];
        if (!method_exists($controller, $action)) diebug($request);

        // Call Action
        $controller->trigger($action);
    }

}