<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\State;

class stateManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getState(int $idState): State {
    $query = $this->getInstance()->prepare('SELECT * FROM state WHERE id = :id');
    $query->execute(['id' => $idState]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $state = new State();
    $state->hydrate($res);
    return $state;
  }

  /** @return State[] */
  public function getStates(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM state LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $states = [];
    foreach ($res as $user) {
      $r = new State();
      $r->hydrate($user);
      $states[] = $r;
    }
    return $states;
  }

}
