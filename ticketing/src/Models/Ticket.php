<?php 
namespace Ticketing\Models;

use DateTime;
use ORM;

class Ticket extends ORM\Entity {
  #[ORM\Id]
  private ?int $id = null;

  // #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy:"tickets")]
  private $utilisateur;
  // #[ORM\ManyToOne(targetEntity: Type::class, inversedBy:"tickets")]
  private $type;
  // #[ORM\ManyToOne(targetEntity: User::class, inversedBy:"tickets")]
  private $priority;
  // #[ORM\ManyToOne(targetEntity: User::class, inversedBy:"tickets")]
  private $state;
  // #[ORM\ManyToOne(targetEntity: User::class, inversedBy:"tickets")]
  private $traitements;
  #[ORM\Column(type:"string", nullable: false)]
  private ?string $subject = null;
  #[ORM\Column(type:"string", nullable: true)]
  private ?string $description = null;
  #[ORM\Column(type:"string", nullable: true)]
  private ?string $filepath = null;
  #[ORM\Column(type:"datetime", nullable: false, default: true)]
  private DateTime $creation;
  #[ORM\Column(type:"datetime", nullable: false, default: true)]
  private DateTime $update;


  public function getId(): ?int { return $this->id; }
  public function getUtilisateur(): Utilisateur { return $this->utilisateur; }
  public function getType(): Type { return $this->type; }
  public function getPriority(): int { return $this->priority; }
  public function getState(): int { return $this->state; }
  public function getTraitements(): int { return $this->traitements; }
  public function getSubject(): ?string { return $this->subject; }
  public function getDescription(): ?string { return $this->description; }
  public function getFilepath(): ?string { return $this->filepath; }
  public function getCreation(): DateTime { return $this->creation; }
  public function getUpdate(): DateTime { return $this->update; }


  public function setUtilisateur(Utilisateur $utilisateur): void { $this->utilisateur = $utilisateur; }
  public function setType(Type $type): void { $this->type = $type; }
  public function setPriority(int $priority): void { $this->priority = $priority; }
  public function setState(int $state): void { $this->state = $state; }
  public function setTraitements(int $traitements): void { $this->traitements = $traitements; }
  public function setSubject(string $subject): void { $this->subject = $subject; }
  public function setDescription(string $description): void { $this->description = $description; }
  public function setFilepath(string $filepath): void { $this->filepath = $filepath; }
  public function setCreation(DateTime $creation): void { $this->creation = $creation; }
  public function setUpdate(DateTime $update): void { $this->update = $update; }
}