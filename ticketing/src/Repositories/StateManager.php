<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\State;

class StateManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getState(int $idState): State {
    $query = $this->getInstance()->prepare('SELECT * FROM state WHERE id = :id');
    $query->execute(['id' => $idState]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $state = new State($res);
    return $state;
  }

  /** @return State[] */
  public function getStates(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('states', State::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

  public function findState(string $name): ?State {
    $query = $this->getInstance()->prepare('SELECT * FROM state WHERE name = :name');
    $query->execute(['name' => $name]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    if ($res === false) {
      return null;
    }

    $state = new State($res);
    return $state;
  }
}
