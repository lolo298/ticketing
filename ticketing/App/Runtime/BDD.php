<?php 
namespace Runtime;

class BDD {
  private static $_instance = null;

  private static \PDO $_pdo;

  private int $transactions_level = 0;

  private function __construct() {
    $dbAddress = $_ENV["DB_ADDRESS"];
    $this::$_pdo = new \PDO("mysql:host=$dbAddress;port=3306;dbname=ticketing", 'root', 'root_password');
  }

  public function getPdo(): \PDO { return $this::$_pdo; }

  public static function getInstance(): self {
    if (self::$_instance === null) {
      self::$_instance = new BDD();
    }
    return self::$_instance;
  }


  public function beginTransaction(): void {
    if ($this->transactions_level == 0) {
      $this::$_pdo->beginTransaction();
    }
    $this->transactions_level++;
  }

  public function rollback(): void {
    $this->transactions_level = 0;
    $this::$_pdo->rollBack();
  }

  public function commit(): void {
    $this->transactions_level--;
    if ($this->transactions_level == 0) {
      $this::$_pdo->commit();
    } else if ($this->transactions_level < 0) {
      throw new \Exception('Commit without beginTransaction');
    }
  }

  
}