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
  public function getTypes(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('type', Type::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

  public function findType(string $name): ?Type {
    $query = $this->getInstance()->prepare('SELECT * FROM type WHERE name = :name');
    $query->execute(['name' => $name]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    if ($res === false) {
      return null;
    }

    $type = new Type($res);
    return $type;
  }
}
