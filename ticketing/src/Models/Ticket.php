<?php 
namespace Ticketing\Models;

use DateTime;
use ORM;

class Ticket extends ORM\Entity {
  #[ORM\Id]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy:"tickets")]
  private $utilisateur;
  #[ORM\ManyToOne(targetEntity: Type::class, inversedBy:"tickets")]
  private $type;
  #[ORM\ManyToOne(targetEntity: Priority::class, inversedBy:"tickets")]
  private $priority;
  #[ORM\ManyToOne(targetEntity: State::class, inversedBy:"tickets")]
  private $state;
  #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy:"tickets")]
  private $traitements;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private ?string $subject = null;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR, nullable: true)]
  private ?string $description = null;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR, nullable: true)]
  private ?string $filepath = null;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $creationDate;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $updateDate;


  public function getId(): ?int { return $this->id; }
  public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
  public function getType(): Type { return $this->type; }
  public function getPriority(): Priority { return $this->priority; }
  public function getState(): State { return $this->state; }
  public function getTraitements(): int { return $this->traitements; }
  public function getSubject(): ?string { return $this->subject; }
  public function getDescription(): ?string { return $this->description; }
  public function getFilepath(): ?string { return $this->filepath; }
  public function getCreationDate(): DateTime { return $this->creationDate; }
  public function getUpdateDate(): DateTime { return $this->updateDate; }


  public function setId(int $id): void { $this->id = $id; }
  public function setUtilisateur(Utilisateur $utilisateur): void { $this->utilisateur = $utilisateur; }
  public function setType(Type $type): void { $this->type = $type; }
  public function setPriority(Priority $priority): void { $this->priority = $priority; }
  public function setState(State $state): void { $this->state = $state; }
  public function setTraitements(int $traitements): void { $this->traitements = $traitements; }
  public function setSubject(string $subject): void { $this->subject = $subject; }
  public function setDescription(string $description): void { $this->description = $description; }
  public function setFilepath(string $filepath): void { $this->filepath = $filepath; }
  public function setCreationDate(DateTime $creation): void { $this->creationDate = $creation; }
  public function setUpdateDate(DateTime $update): void { $this->updateDate = $update; }
}