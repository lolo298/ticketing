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
  private RoleManager $roleManager;

  public function __construct(UtilisateurManager $userManager, RoleManager $roleManager) {
    $this->userManager = $userManager;
    $this->roleManager = $roleManager;
  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    $this->render('auth/login');
  }

  #[Route('/register', 'GET', 'register')]
  public function register(): void {
    $roles = $this->roleManager->getRoles();
    $roles = array_filter($roles, fn($role) => $role->getId() !== 1);
    
    $this->render('auth/register', ['roles' => $roles]);
  }

  #[Route('/login', 'POST', 'login')]
  public function loginPost(): void {
    $user = $_POST['login'];
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

    if (!$user->getActif()) {
      $this->render('auth/login', ['error' => 'Utilisateur inactif']);
      return;
    }

    self::$session->connect($user);
    header('Location: /');
    die();
  }

  #[Route('/register', 'POST', 'register')]
  public function registerPost(): void {
    $data = $_POST;
    
    $user = $this->userManager->findUser($data['login']);

    if ($user) {
      $this->render('auth/register', ['error' => 'Utilisateur déjà existant']);
      return;
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

    $role = $this->roleManager->getRole((int)$data['role']);

    unset($data['role']);

    $user = new Utilisateur($data);
    $user->setRole($role);
    $user->setActif(false);
    $user->save();
    header('Location: /login');
    die();
  }

  #[Route('/logout', 'GET', 'logout')]
  public function logout(): void {
    self::$session->disconnect();
    header('Location: /login');
    die();
  }
}