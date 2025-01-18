<?php
namespace Ticketing\Repositories;

use Runtime\Manager;
use Ticketing\Models\Ticket;

class TicketManager extends Manager {

  public function __construct() {
    parent::__construct();
  }

  public function getTicket(int $idTicket): Ticket {
    $query = $this->getInstance()->prepare('SELECT * FROM ticket WHERE id = :id');
    $query->execute(['id' => $idTicket]);
    $res = $query->fetch(\PDO::FETCH_ASSOC);

    $ticket = new Ticket($res);
    return $ticket;
  }

  /** @return Ticket[] */
  public function getTickets(int $limit = 0, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC', string $extra = ""): array {
    return $this->getValues('ticket', Ticket::class, $limit, $offset, $sortBy, $sortDirection, $extra);
  }

}
