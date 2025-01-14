<?php

namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Ticketing\Models\Ticket;
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

  public function __construct(TypeManager $typeManager, PriorityManager $priorityManager, StateManager $stateManager, TicketManager $ticketManager) {
    $this->typeManager = $typeManager;
    $this->priorityManager = $priorityManager;
    $this->stateManager = $stateManager;
    $this->ticketManager = $ticketManager;
  }



  #[Route('/', 'GET', 'home')]
  public function home(): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }
    $tickets = $this->ticketManager->getTickets(sortBy: 'creation_date', sortDirection: 'DESC', extra: 'id_utilisateur = ' . self::$session->getUser()->getId());
    $types = $this->typeManager->getTypes();

    $this->render('home', ['tickets' => $tickets, 'types' => $types]);
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

    $this->render("ticket", ['ticket' => $ticket]);
  }

  #[Route('/api/edit/ticket/{id}', 'POST', 'updateTicket')]
  public function updateTicket(array $params): void {
    if (self::$session->isConnected() === false) {
      header('Location: /login');
      die();
    }
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
