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
    $tickets = $this->ticketManager->getTickets(sortBy: 'creation_date', sortDirection: 'DESC');
    // $ticket->save();
    
    $this->render('home', ['tickets' => $tickets]);
  }

  #[Route('/api/newTicket', 'POST','newTicket')]
  public function newTicket() {
    $ticket = new Ticket($_POST);
    $user = $this->userManager->getUser($_COOKIE['user'] ?? 1);
    $type = $this->typeManager->getType(1);
    $priority = $this->priorityManager->getPriority(1);
    $state = $this->stateManager->getState(1);

    $ticket->setUtilisateur($user);
    $ticket->setType($type);
    $ticket->setPriority($priority);
    $ticket->setState($state);

    $ticket->save();

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    

  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    var_dump('login');
  }
  
  #[Route('/ticket/{id}','GET','ticket')]
  public function ticket(array $params): void {
    $ticket = $this->ticketManager->getTicket($params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
     }

    $this->render("ticket", ['ticket' => $ticket]);
  }

  #[Route('/api/edit/ticket/{id}','POST','updateTicket')]
  public function updateTicket(array $params): void {
    $ticket = $this->ticketManager->getTicket($params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
     }

    $this->ticketManager->getTicket($params['id']);
    $ticket->hydrate($_POST);

    try {  
      $ticket->save();
    } catch (\Exception $e) {
      http_response_code(500);
      echo $e->getMessage();
    }
  }
}