<?php
namespace Ticketing\Models;

use ORM;
use \DateTime;

#[ORM\Serializable(target: 'login')]
class Utilisateur extends ORM\SerializableEntity {
  #[ORM\Id]
  private ?int $id;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $login;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $password;
  #[ORM\ManyToOne(targetEntity: Role::class, inversedBy:"utilisateurs")]
  private Role $role;
  #[ORM\Column(type:ORM\ColumnType::BOOLEAN)]
  private bool $actif;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $creationDate;

  public function getId(): int { return $this->id; }
  public function getLogin(): string { return $this->login; }
  public function getPassword(): string { return $this->password; }
  public function getRole(): Role { return $this->role; }
  public function getActif(): bool { return $this->actif; }
  public function getCreationDate(): DateTime { return $this->creationDate; }
  
  public function setId(null|int $id): void { $this->id = $id; }
  public function setLogin(string $login): void { $this->login = $login; }
  public function setPassword(string $password): void { $this->password = $password; }
  public function setRole(Role $role): void { $this->role = $role; }
  public function setActif(bool $actif): void { $this->actif = $actif; }
  public function setCreationDate(DateTime $creationDate): void { $this->creationDate = $creationDate; }
}