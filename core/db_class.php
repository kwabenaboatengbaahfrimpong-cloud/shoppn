<?php
// Base database class. Every model class (CustomerClass, ProductClass, CartClass)
// extends this so they all share one connection setup.
class Database {
    protected $conn;

    public function __construct() {
        require_once 'db_cred.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if ($this->conn->connect_error) {
            error_log($this->conn->connect_error);
            die('Connection failed.');
        }
    }
}
