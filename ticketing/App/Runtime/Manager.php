<?php
namespace Runtime;

class Manager {
  private static ?\PDO $instance = null;

  public static function getInstance(): \PDO {
    if (is_null(self::$instance)) {
      self::$instance = new \PDO('mysql:host=db;dbname=ticketing', 'root', 'root_password');
      self::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    return self::$instance;
  }

  public function __construct() {
    self::getInstance();
  }
}