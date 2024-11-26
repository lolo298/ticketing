<?php
namespace Runtime;

class Manager {
  private static ?\PDO $instance = null;

  public static function getInstance(): \PDO {
    if (is_null(self::$instance)) {
      self::$instance = BDD::getInstance()->getPdo();
    }
    return self::$instance;
  }

  public function __construct() {
    self::getInstance();
  }
}