<?php

namespace ANSR\Adapters;

/**
 * @author Ivan Yonkov <ivanynkv@gmail.com>
 */
abstract class Database {

    protected $_host;
    protected $_user;
    protected $_pass;
    protected $_db;
    protected $_conn;

    public function __construct($host, $user, $pass, $db) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_db = $db;

        $this->connect();
        $this->query("SET NAMES 'utf8'");
    }

    protected function connect() {
        
    }

    public function query($query) {
        
    }

    public function fetch($result) {

    }

    public function row($result) {

    }

    public function affectedRows() {

    }

    public function escape($string) {

    }

    public function lastId() {

    }

    public function error() {

    }


}

