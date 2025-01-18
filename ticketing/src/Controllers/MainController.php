<?php

namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Ticketing\Models\Ticket;
use Ticketing\Models\Traitement;
use Ticketing\Repositories\RoleManager;
use Ticketing\Repositories\TypeManager;
use Ticketing\Repositories\UtilisateurManager;
use Ticketing\Repositories\PriorityManager;
use Ticketing\Repositories\StateManager;
use Ticketing\Repositories\TicketManager;

class MainController extends AbstractController {

  private TypeManager $typeManager;
  private PriorityManager $priorityManager;
  private StateManager $stateManager;
  private TicketManager $ticketManager;
  private RoleManager $roleManager;

  public function __construct(TypeManager $typeManager, PriorityManager $priorityManager, StateManager $stateManager, TicketManager $ticketManager, RoleManager $roleManager) {
    $this->typeManager = $typeManager;
    $this->priorityManager = $priorityManager;
    $this->stateManager = $stateManager;
    $this->ticketManager = $ticketManager;
    $this->roleManager = $roleManager;
  }



  #[Route('/', 'GET', 'home')]
  public function home(): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }

    $roleClient = $this->roleManager->findRole('CLIENT');

    if ($roleClient === null) {
      throw new \Exception('Role CLIENT not found');
    }

    if (self::$session->getUser()->getRole()->getId() === $roleClient->getId()) {
      $tickets = $this->ticketManager->getTickets(sortBy: 'creation_date', sortDirection: 'DESC', extra: 'id_utilisateur = ' . self::$session->getUser()->getId());
    } else {
      $tickets = $this->ticketManager->getTickets(sortBy: 'creation_date', sortDirection: 'DESC');
    }

    $types = $this->typeManager->getTypes();
    $priorities = $this->priorityManager->getPriorities();

    $this->render('home', ['tickets' => $tickets, 'types' => $types, 'priorities' => $priorities]);
  }

  #[Route('/api/newTicket', 'POST', 'newTicket')]
  public function newTicket() {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }

    $data = $_POST;

    $user = self::$session->getUser();
    $type = $this->typeManager->getType((int) $data['type']);
    unset($data['type']);
    $priority = $this->priorityManager->getPriority(1);
    $state = $this->stateManager->getState(1);

    $ticket = new Ticket($data);
    $ticket->setUtilisateur($user);
    $ticket->setType($type);
    $ticket->setPriority($priority);
    $ticket->setState($state);
    $ticket->setTraitements([]);

    $ticket->save();

    header('Location: ' . explode('?', $_SERVER['HTTP_REFERER'])[0]);
  }

  #[Route('/ticket/{id}', 'GET', 'ticket')]
  public function ticket(array $params): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }

    $ticket = $this->ticketManager->getTicket($params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
    }

    $traitements = $ticket->getTraitements();
    uasort($traitements, function ($a, $b) {
      return $b->getDate() <=> $a->getDate();
    });


    $this->render("ticket", ['ticket' => $ticket, 'states' => $this->stateManager->getStates(), 'types' => $this->typeManager->getTypes(), 'priorities' => $this->priorityManager->getPriorities(), 'traitements' => $traitements]);
  }

  #[Route('/ticket/{id}/edit', 'POST', 'updateTicket')]
  public function updateTicket(array $params): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }
    $ticket = $this->ticketManager->getTicket((int)$params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
    }
    
    $ticket->setSubject($_POST['subject']);
    $ticket->setDescription($_POST['description']);
    
    $type = $this->typeManager->getType((int)$_POST['type']);
    $ticket->setType($type);
    
    $priority = $this->priorityManager->getPriority((int)$_POST['priority']);
    $ticket->setPriority($priority);
    
    $state = $this->stateManager->getState((int)$_POST['state']);
    $ticket->setState($state);

    $ticket->setUpdateDate(new \DateTime());
    
    try {
      $ticket->save();
    } catch (\Exception $e) {
      http_response_code(500);
      echo $e->getMessage();
    }
  }


  #[Route('/ticket/{id}/edit', 'DELETE', 'closeTicket')]
  public function closeTicket(array $params): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }
    $ticket = $this->ticketManager->getTicket((int)$params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
    }

    $closedState = $this->stateManager->findState('CLOSED');

    if ($closedState === null) {
      throw new \Exception('State CLOSED not found');
    }

    $ticket->setState($closedState);


    try {
      $ticket->save();
    } catch (\Exception $e) {
      http_response_code(500);
      echo $e->getMessage();
    }
  }

  #[Route('/ticket/{id}/chat', 'POST', 'sendChat')]
  public function sendChat(array $params): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }
    $ticket = $this->ticketManager->getTicket($params['id']);
    if ($ticket->getId() === null) {
      header("Location: /");
      die();
    }

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);

    $newTraitement = new Traitement($data);
    $newTraitement->setTicket($ticket);

    try {
      $newTraitement->save();
    } catch (\Exception $e) {
      http_response_code(500);
      echo $e->getMessage();
    }
  }
}
