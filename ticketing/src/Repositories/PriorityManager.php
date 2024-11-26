<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Priority;

class PriorityManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getPriority(int $idPriority): Priority {
    $query = $this->getInstance()->prepare('SELECT * FROM priority WHERE id = :id');
    $query->execute(['id' => $idPriority]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $priority = new Priority($res);
    return $priority;
  }

  /** @return Priority[] */
  public function getPriorities(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM priority LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $priorities = [];
    foreach ($res as $data) {
      $r = new Priority($data);
      $priorities[] = $r;
    }
    return $priorities;
  }

}
