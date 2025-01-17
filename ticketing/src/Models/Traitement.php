<?php 
namespace Ticketing\Models;

use DateTime;
use ORM;

class Traitement extends ORM\Entity {
  #[ORM\Id]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: self::class)]
  private $ticket;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private ?string $message = null;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $date;


  public function getId(): ?int { return $this->id; }
  public function getTicket(): Ticket { return $this->ticket; }
  public function getMessage(): ?string { return $this->message; }
  public function getDate(): DateTime { return $this->date; }


  public function setId(int $id): void { $this->id = $id; }
  public function setTicket(Ticket $ticket): void { $this->ticket = $ticket; }
  public function setMessage(string $message): void { $this->message = $message; }
  public function setDate(DateTime $messageDate): void { $this->date = $messageDate; }
}