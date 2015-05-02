<?php

namespace ANSR\Routing;

/**
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
class DefaultRouter implements IRouter {

    const REQUEST_URI_CONTROLLER = 2;
    const REQUEST_URI_ACTION = 3;

    const DEFAULT_CONTROLLER = 'Welcome';
    const DEFAULT_ACTION = 'index';

    public function getController() {
        $uri = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($uri[self::REQUEST_URI_CONTROLLER])) {
            return ucfirst($uri[self::REQUEST_URI_CONTROLLER]);
        }

        return self::DEFAULT_CONTROLLER;
    }

    public function getAction() {
        $uri = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($uri[self::REQUEST_URI_ACTION])) {
            return $uri[self::REQUEST_URI_ACTION];
        }

        return self::DEFAULT_ACTION;
    }

    public function registerRequest() {
        $request = explode('/', $_SERVER['REQUEST_URI']);
        $params = array();
        foreach ($request as $key => $param) {
            if ($key > self::REQUEST_URI_ACTION) {
                if (isset($request[$key], $request[$key + 1])) {
                    $params[$request[$key]] = $request[$key + 1];
                    unset($request[$key + 1]);
                }
            }
        }
        \ANSR\Library\Registry\Registry::set('request', $params);
    }

}