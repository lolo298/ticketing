<?php 
namespace Runtime;

class BDD {
  private static $_instance = null;
  private $_pdo;

  private function __construct() {
    $this->_pdo = new \PDO('mysql:host=db;dbname=ticketing', 'root', 'root_password');
  }

  public static function getInstance(): \PDO {
    if (self::$_instance === null) {
      self::$_instance = new BDD();
    }
    return self::$_instance->_pdo;
  }


  
}