<?php
namespace Runtime;

use Ticketing\Models\Priority;

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

  protected function getValues(string $table, string $class, int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    $query = $this->getInstance()->prepare("SELECT * FROM $table " . ($extra !== "" ? "WHERE $extra " : "") . "ORDER BY $sortBy $sortDirection " . ($limit > 0 ? 'LIMIT :limit OFFSET :offset' : ''));
    if ($limit > 0) {
      $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
      $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
    }
    $query->execute();
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $priorities = [];
    foreach ($res as $data) {
      $r = new $class($data);
      $priorities[] = $r;
    }
    return $priorities;
  }

}