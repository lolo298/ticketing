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
  public function getPriorities(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('priority', Priority::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

  public function findPriority(string $name): ?Priority {
    $query = $this->getInstance()->prepare('SELECT * FROM priority WHERE name = :name');
    $query->execute(['name' => $name]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    if ($res === false) {
      return null;
    }

    $priority = new Priority($res);
    return $priority;
  }

}
