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
  public function getUsers(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('utilisateur', Utilisateur::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

  public function findUser(string $username): ?Utilisateur {
    $query = $this->getInstance()->prepare('SELECT * FROM utilisateur WHERE login = :username');
    $query->execute(['username' => $username]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);
    if (!$res) {
      return null;
    }

    $user = new Utilisateur($res);
    return $user;
  }

}
