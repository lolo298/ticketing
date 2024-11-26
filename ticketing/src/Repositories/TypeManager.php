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
  public function getPriorities(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM type LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $types = [];
    foreach ($res as $data) {
      $r = new Type($data);
      $types[] = $r;
    }
    return $types;
  }

}
