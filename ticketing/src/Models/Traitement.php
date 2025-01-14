<?php 
namespace Ticketing\Models;

use DateTime;
use ORM;

class Traitement extends ORM\Entity {
  #[ORM\Id]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy:"tickets")]
  private $ticket;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private ?string $response = null;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $responseDate;


  public function getId(): ?int { return $this->id; }
  public function getTicket(): Ticket { return $this->ticket; }
  public function getReponse(): ?string { return $this->response; }
  public function getResponseDate(): DateTime { return $this->responseDate; }


  public function setId(int $id): void { $this->id = $id; }
  public function setTicket(Ticket $ticket): void { $this->ticket = $ticket; }
  public function setResponse(string $response): void { $this->response = $response; }
  public function setResponseDate(DateTime $responseDate): void { $this->responseDate = $responseDate; }
}