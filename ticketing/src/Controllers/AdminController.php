<?php

namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Ticketing\Models\Utilisateur;
use Ticketing\Repositories\RoleManager;
use Ticketing\Repositories\TypeManager;
use Ticketing\Repositories\UtilisateurManager;
use Ticketing\Repositories\PriorityManager;
use Ticketing\Repositories\StateManager;
use Ticketing\Repositories\TicketManager;

class AdminController extends AbstractController {

  private UtilisateurManager $userManager;
  private RoleManager $roleManager;
  public function __construct(UtilisateurManager $utilisateurManager, RoleManager $roleManager) {
    $this->userManager = $utilisateurManager;
    $this->roleManager = $roleManager;
  }

  #[Route('/admin/users', 'GET', 'users')]
  public function users(): void {
    if (!self::$session->isConnected() || !self::$session->isAdmin()) {
      header('Location: /login');
      die();
    }
    $error = '';
    if (isset($_GET['error'])) {
      $error = $_GET['error'];
    }


    $users = $this->userManager->getUsers(10, 0);
    $roles = $this->roleManager->getRoles(10, 0);
    $this->render('users', ['users' => $users, 'roles' => $roles, 'error' => $error]);
  }


  #[Route('/newUser', 'POST', 'newUser')]
  public function newUser(): void {
    $data = $_POST;

    if ($this->userManager->findUser($data['login'])) {

      header('Location: ' . explode('?', $_SERVER['HTTP_REFERER'])[0] . '?error=' . urlencode(('Utilisateur déjà existant')));
      die();
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $role = $this->roleManager->getRole((int)$data['role']);

    unset($data['role']);

    $user = new Utilisateur($data);
    $user->setRole($role);
    $user->setActif(false);
    $user->save();
    header('Location: ' . explode('?', $_SERVER['HTTP_REFERER'])[0]);
  }

  #[Route('/admin/user/{id}', 'PATCH', 'editUser')]
  public function editUser($params): void {
    if (!self::$session->isConnected() || !self::$session->isAdmin()) {
      header('Location: /login');
      die();
    }

    $data = json_decode(file_get_contents('php://input'), true);

    $user = $this->userManager->getUser($params['id']);
    foreach ($data as $key => $value) {
      $method = 'set' . ucfirst($key);
      if (method_exists($user, $method)) {
        if ($key === 'role') {
          $role = $this->roleManager->getRole((int)$value);
          $user->setRole($role);
          continue;
        }
        $user->$method($value);
      }
    }

    $user->save();
  }

}
