<?php
namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Runtime\BDD;
use Ticketing\Models\Ticket;
use Ticketing\Models\Utilisateur;
use Ticketing\Repositories\RoleManager;
use Ticketing\Repositories\TypeManager;
use Ticketing\Repositories\UtilisateurManager;
use Ticketing\Repositories\PriorityManager;
use Ticketing\Repositories\StateManager;
use Ticketing\Repositories\TicketManager;

class MainController extends AbstractController {

  private UtilisateurManager $userManager;
  private RoleManager $roleManager;
  private TypeManager $typeManager;
  private PriorityManager $priorityManager;
  private StateManager $stateManager;
  private TicketManager $ticketManager;

  public function __construct(UtilisateurManager $utilisateurManager, RoleManager $roleManager, TypeManager $typeManager, PriorityManager $priorityManager, StateManager $stateManager, TicketManager $ticketManager) {
    $this->userManager = $utilisateurManager;
    $this->roleManager = $roleManager;
    $this->typeManager = $typeManager;
    $this->priorityManager = $priorityManager;
    $this->stateManager = $stateManager;
    $this->ticketManager = $ticketManager;
  }



  #[Route('/', 'GET', 'home')]
  public function home(): void {
    try{
      $bdd = BDD::getInstance();
    } catch (\PDOException $e) {
      echo 'Connexion échouée : ' . $e->getMessage();
      die();
    }
    
    $tickets = $this->ticketManager->getTickets();
    // $ticket->save();
    
    $this->render('home', ['tickets' => $tickets]);
  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    var_dump('login');
  }
  
}