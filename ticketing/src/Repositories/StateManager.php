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
  public function getStates(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM state LIMIT :limit OFFSET :offset');
    $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $query->execute();
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $states = [];
    foreach ($res as $data) {
      $r = new State($data);
      $states[] = $r;
    }
    return $states;
  }

}
