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
  public function getTickets(int $limit = 10, int $offset = 0, string $sortBy = 'id', string $sortDirection = 'ASC'): array {
    $query = $this->getInstance()->prepare("SELECT * FROM ticket ORDER BY $sortBy $sortDirection LIMIT :limit OFFSET :offset");
    $query->bindParam(':limit', $limit, \PDO::PARAM_INT);
    $query->bindParam(':offset', $offset, \PDO::PARAM_INT);

    $query->execute();
    $res = $query->fetchAll(\PDO::FETCH_ASSOC);
    $tickets = [];
    foreach ($res as $data) {
      $r = new Ticket($data);
      $tickets[] = $r;
    }
    return $tickets;
  }

}
