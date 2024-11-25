<?php 
namespace Runtime;

class BDD {
  private static $_instance = null;

  private static \PDO $_pdo;

  private int $transactions_level = 0;

  private function __construct() {
    $this::$_pdo = new \PDO('mysql:host=db;dbname=ticketing', 'root', 'root_password');
  }

  public function getPdo(): \PDO { return $this::$_pdo; }

  public static function getInstance(): self {
    if (self::$_instance === null) {
      self::$_instance = new BDD();
    }
    return self::$_instance;
  }


  public function beginTransaction(): void {
    echo "<span style='color: red;font-size: 32px'>Begin transaction of level " . $this->transactions_level . "</span><br>";
    if ($this->transactions_level == 0) {
      echo "<span style='color: red;font-size: 32px'>Begin transaction to db</span><br>";
      $this::$_pdo->beginTransaction();
    }
    $this->transactions_level++;
  }

  public function rollback(): void {
    echo "<span style='color: red;font-size: 32px'>Rollback transaction of level " . $this->transactions_level . "</span><br>";
    $this->transactions_level--;
    if ($this->transactions_level == 0) {
      echo "<span style='color: red;font-size: 32px'>Rollbacking to db</span><br>";
      $this::$_pdo->rollBack();
    } else if ($this->transactions_level < 0) {
      throw new \Exception('Rollback without beginTransaction');
    }
    echo "new level : " . $this->transactions_level . "<br>";
  }

  public function commit(): void {
    echo "<span style='color: green;font-size: 32px'>Commit transaction of level " . $this->transactions_level . "</span><br>";
    $this->transactions_level--;
    if ($this->transactions_level == 0) {
      echo "<span style='color: green;font-size: 32px'>Commiting to db</span><br>";
      $this::$_pdo->commit();
    } else if ($this->transactions_level < 0) {
      throw new \Exception('Commit without beginTransaction');
    }
    echo "new level : " . $this->transactions_level . "<br>";
  }

  
}