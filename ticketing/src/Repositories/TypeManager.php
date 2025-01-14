<?php

namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Type;

class TypeManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getType(int $idType): Type {
    $query = $this->getInstance()->prepare('SELECT * FROM type WHERE id = :id');
    $query->execute(['id' => $idType]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $type = new Type($res);
    return $type;
  }

  /** @return Type[] */
  public function getTypes(int $limit = 0, int $offset = 0): array {
    $query = $this->getInstance()->prepare('SELECT * FROM type ' . ($limit > 0 ? 'LIMIT :limit OFFSET :offset' : ''));
    if ($limit > 0) {
      $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
      $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
    }
    $query->execute();
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $types = [];
    foreach ($res as $data) {
      $r = new Type($data);
      $types[] = $r;
    }
    return $types;
  }
}
