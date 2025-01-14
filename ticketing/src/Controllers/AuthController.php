<?php
namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Runtime\Session;
use Ticketing\Models\Utilisateur;
use Ticketing\Repositories\RoleManager;
use Ticketing\Repositories\UtilisateurManager;

class AuthController extends AbstractController {
  private UtilisateurManager $userManager;

  public function __construct(UtilisateurManager $userManager) {
    $this->userManager = $userManager;
  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    $this->render('auth/login');
  }

  #[Route('/login', 'POST', 'login')]
  public function loginPost(): void {
    $user = $_POST['username'];
    $password = $_POST['password'];

    $user = $this->userManager->findUser($user);

    if (!$user) {
      $this->render('auth/login', ['error' => 'Utilisateur inconnu']);
      return;
    }

    if (!password_verify($password, $user->getPassword())) {
      $this->render('auth/login', ['error' => 'Mot de passe incorrect']);
      return;
    }
    self::$session->connect($user);
    header('Location: /');
    die();
  }

  #[Route('/logout', 'GET', 'logout')]
  public function logout(): void {
    self::$session->disconnect();
    header('Location: /login');
    die();
  }
}