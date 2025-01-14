<?php

namespace Runtime;

use Ticketing\Models\Utilisateur;
use Ticketing\Repositories\RoleManager;
use Ticketing\Repositories\UtilisateurManager;
use Runtime\Singleton;

class Session implements Singleton {
  private static ?Session $instance = null;

  private RoleManager $roleManager;
  private UtilisateurManager $userManager;

  private Utilisateur $user;


  public function __construct(UtilisateurManager $userManager, RoleManager $roleManager) {
    $this->roleManager = $roleManager;
    $this->userManager = $userManager;

    if (isset($_COOKIE['user'])) {
      $user = $userManager->getUser($_COOKIE['user']);
      if ($user !== null) {
        $this->connect($user);
      }
    }
  }

  public static function getInstance(): Session {
    if (is_null(self::$instance)) {
      self::$instance = new Session(new UtilisateurManager(), new RoleManager());
    }
    return self::$instance;
  }

  public function isConnected(): bool {
    return isset($this->user);
  }

  public function isAdmin(): bool {
    if (!$this->isConnected()) {
      return false;
    }

    return $this->user->getRole()->getId() === 1;
  }

  public function connect(Utilisateur $user): void {
    setcookie('user', $user->getId(), time() + 3600, "/", "", false, true);
    $this->user = $user;
  }

  public function disconnect(): void {
    setcookie('user', '', time() - 3600, "/", "", false, true);
    unset($this->user);
  }

  public function getUser(): Utilisateur {
    return $this->user;
  }
}
