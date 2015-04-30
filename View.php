<?php

namespace ANSR;

/**
 * @author Ivan Tonkov <ivanynkv@gmail.com>
 */
class View {

    private $_frontController;

    const VIEW_FOLDER = 'Views';
    const VIEW_DEFAULT = 'index';
    const VIEW_PARTIALS = 'partials';
    
    const CSS_FOLDER = 'css';
    const SCRIPTS_FOLDER = 'js';
    
    private static $_styles = [];
    private static $_scripts = [];
    private static $_header;
    private static $_footer;
    private static $_aside;

    public function setFrontController(\ANSR\Dispatcher\FrontController $fronController) {
        $this->_frontController = $fronController;
    }

    /**
     * @return \ANSR\Dispatcher\FrontController
     */
    public function getFrontController() {
        return $this->_frontController;
    }

    /**
     * @return string (Template's path and filename)
     */
    public function getTemplate() {
        $action = !empty($this->getFrontController()->getRouter()->getAction()) ? $this->getFrontController()->getRouter()->getAction() : self::VIEW_DEFAULT;
        return self::VIEW_FOLDER
                . DIRECTORY_SEPARATOR
                . strtolower($this->getFrontController()->getRouter()->getController())
                . DIRECTORY_SEPARATOR
                . strtolower($action)
                . '.php';
    }

    /**
     * Includes header if one is set
     * @return void
     */
    public function initHeader() {
        if (self::$_header) {
            $this->partial(self::$_header);
        }
    }
    
    /**
     * Includes template
     * @return void
     */
    public function initTemplate() {
        require_once $this->getTemplate();
    }
    
    /**
     * Includes footer if one is set
     * @return void
     */
    public function initFooter() {
        if (self::$_footer) {
            $this->partial(self::$_footer);
        }
    }

    /**
     * Includes aside menu if one is set
     * @return void
     */
    public function initAside() {
        if (self::$_aside) {
            $this->partial(self::$_aside);
        }
    }

    public function partial($name) {
        $partial = self::VIEW_FOLDER
                . DIRECTORY_SEPARATOR
                . self::VIEW_PARTIALS
                . DIRECTORY_SEPARATOR
                . $name;
        if (is_file($partial)) {
            include $partial;
        }
    }
    
    /**
     * Generates URL depending on the given controller, action and params
     * @param string $controller
     * @param string $action
     * @param string $paramName
     * @param string $paramValue
     * @return string
     */
    public function url($controller, $action = 'index', $paramName = null, $paramValue = null) {
        $url = HOST . $controller . '/' . $action;
        
        if ($paramName) {
            $url .= '/' . $paramName . '/' . $paramValue;
        }
        
        return $url;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    public function __get($name) {
        return $this->$name;
    }
    
    /**
     * Add styles to the global static styles array
     * 
     * @param string $filename
     * @param string $dir
     * @return void
     */
    public static function addStyle($filename, $dir = null) {
        if (!$dir) {
            $dir = HOST . self::VIEW_FOLDER . '/' . self::CSS_FOLDER;
        }
        
        $style = $dir . '/' . $filename;
        
        self::$_styles[] = "<link rel='stylesheet' type='text/css' href='$style'>";
    }
    
    /**
     * Add scripts to the global static scripts array
     * 
     * @param string $filename
     * @param string $dir
     * @return void
     */
    public static function addScripts($filename, $dir = null) {
        if (!$dir) {
            $dir = HOST . self::VIEW_FOLDER . '/' . self::SCRIPTS_FOLDER;
        }
        
        $script = $dir . '/' . $filename;
        
        self::$_scripts[] = "<script src='$script'></script>";
    }
    
    /**
     * @return array
     */
    public function getStyles() {
        return self::$_styles;
    }
    
    /**
     * @return array
     */
    public function getScripts() {
        return self::$_scripts;
    }
    
    public static function setHeader($header) {
        self::$_header = $header;
    }
    
    public static function setFooter($footer) {
        self::$_footer = $footer;
    }

    public static function setAside($aside) {
        self::$_aside = $aside;
    }
}