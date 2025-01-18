<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Role;

class RoleManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getRole(int $idRole): Role {
    $query = $this->getInstance()->prepare('SELECT * FROM role WHERE id = :id');
    $query->execute(['id' => $idRole]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $role = new Role($res);
    return $role;
  }

  /** @return Role[] */
  public function getRoles(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('role', Role::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

  public function findRole(string $name): ?Role {
    $query = $this->getInstance()->prepare('SELECT * FROM role WHERE name = :name');
    $query->execute(['name' => $name]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    if ($res === false) {
      return null;
    }

    $role = new Role($res);
    return $role;
  }
}
