<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Utilisateur;

class UtilisateurManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getUser(int $idUtilisateur): Utilisateur {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur WHERE id = :id');
    $query->execute(['id' => $idUtilisateur]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    // var_dump($res);
    $user = new Utilisateur($res);
    return $user;
  }

  /** @return Utilisateur[] */
  public function getUsers(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $users = [];
    foreach ($res as $data) {
      $u = new Utilisateur($data);
      $users[] = $u;
    }
    return $users;
  }

}
