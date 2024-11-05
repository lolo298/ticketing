<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Ticket;

class ticketManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getTicket(int $idTicket): Ticket {
    $query = $this->getInstance()->prepare('SELECT * FROM ticket WHERE id = :id');
    $query->execute(['id' => $idTicket]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $ticket = new Ticket();
    $ticket->hydrate($res);
    return $ticket;
  }

  /** @return Ticket[] */
  public function getTickets(int $limit, int $offset): array {
    $query = $this->getInstance()->prepare('SELECT * FROM ticket LIMIT :limit OFFSET :offset');
    $query->execute(['limit'=> $limit,'offset'=> $offset]);
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $tickets = [];
    foreach ($res as $user) {
      $r = new Ticket();
      $r->hydrate($user);
      $tickets[] = $r;
    }
    return $tickets;
  }

}
