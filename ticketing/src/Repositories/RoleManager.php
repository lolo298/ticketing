<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Role;

class roleManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getRole(int $idRole): Role {
    $query = $this->getInstance()->prepare('SELECT * FROM role WHERE id = :id');
    $query->execute(['id' => $idRole]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $role = new Role();
    $role->hydrate($res);
    return $role;
  }

  /** @return Role[] */
  public function getRoles(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM role LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $roles = [];
    foreach ($res as $user) {
      $r = new Role();
      $r->hydrate($user);
      $roles[] = $r;
    }
    return $roles;
  }

}
