<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Utilisateur;

class UtilisateurManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getUser(int $idUtilisateur): ?Utilisateur {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur WHERE id = :id');
    $query->execute(['id' => $idUtilisateur]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    if (!$res) {
      return null;
    }
    
    $user = new Utilisateur($res);
    return $user;
  }

  /** @return Utilisateur[] */
  public function getUsers(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur LIMIT :limit OFFSET :offset');
    $query->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $query->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $query->execute();
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $users = [];
    foreach ($res as $data) {
      $u = new Utilisateur($data);
      $users[] = $u;
    }
    return $users;
  }

  public function findUser(string $username): Utilisateur {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur WHERE login = :username');
    $query->execute(['username' => $username]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $user = new Utilisateur($res);
    return $user;
  }

}
