<?php

namespace ANSR\Library\Request;

class Post {
    
    /**
     * @var array
     */
    private $_params;
    
    public function __construct(array $params) {
        $this->_params = $params;
    }
    
    public function getParams() {
        return $this->_params;
    }
    
    public function getParam($param) {
        if (!isset($this->_params[$param])) {
            return false;
        }
        return $this->_params[$param];
    }
}