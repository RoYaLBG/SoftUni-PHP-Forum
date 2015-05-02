<?php

namespace ANSR\Controllers;

/**
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
abstract class Controller {

    private $_app;
    private $_view;
    private $_request;
    
    protected $isAdmin = false;

    protected $antiForgeryToken;

    const CSRF_TOKEN_KEY = 'anti-forgery-token';
    
    public function __construct(\ANSR\App $app, \ANSR\View $view, \ANSR\Library\Request\Request $request) {
        $this->_app = $app;
        $this->_view = $view;
        $this->_request = $request;
        $this->init();
    }

    /**
     * Includes the apropriate view
     * @return void
     */
    public function render() {
        $this->getView()->initHeader();
        $this->getView()->initAside();
        $this->getView()->initTemplate();
        $this->getView()->initFooter();
    }

    protected function init() {
        if ($this->getApp()->UserModel->isLogged()) {
            $this->getApp()->UserModel->updateLastClick($_SESSION['user_id'], $this->getView()->getFrontController()->getRouter()->getController() . '/' .  $this->getView()->getFrontController()->getRouter()->getAction());
            $this->isAdmin = $this->getApp()->UserModel->isAdmin(isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0);
        }
        if (!isset($_SESSION[self::CSRF_TOKEN_KEY])) {
            $_SESSION[self::CSRF_TOKEN_KEY] = md5(microtime());
        }
        if (empty($this->getRequest()->getPost()->getParams())) {
            $_SESSION[self::CSRF_TOKEN_KEY] = md5(microtime());
        }
        $this->antiForgeryToken = $_SESSION[self::CSRF_TOKEN_KEY];
        $this->getView()->csrfValidator = '<input name="'.self::CSRF_TOKEN_KEY.'" id="'.self::CSRF_TOKEN_KEY.'" type="hidden" value="'. $this->antiForgeryToken .'" />';
        $this->getView()->csrfJquery = "'anti-forgery-token': '" . $this->antiForgeryToken . "'";
    }

    public function isCsrfTokenValid() {
        if (false != ($token = $this->getRequest()->getPost()->getParam(self::CSRF_TOKEN_KEY))) {
            return $token == $this->antiForgeryToken;
        }

        return false;
    }

    /**
     * @return \ANSR\App
     */
    public function getApp() {
        return $this->_app;
    }

    /**
     * @return \ANSR\View
     */
    protected function getView() {
        return $this->_view;
    }
    
    /**
     * @return \ANSR\Library\Request\Request
     */
    protected function getRequest() {
        return $this->_request;
    }

    protected function redirect($controller, $action = null, $requestParam = null, $requestValue = null) {
        $url = HOST . $controller;
        if ($action) {
            $url .= '/' . $action;
        }

        if ($requestParam) {
            $url .= '/' . $requestParam;
        }

        if ($requestValue) {
            $url .= '/' . $requestValue;
        }

        header("Location: " . $url);
        exit;
    }


}
