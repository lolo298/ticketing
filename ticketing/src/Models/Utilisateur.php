<?php
namespace Ticketing\Models;

use ORM;
use \DateTime;

class Utilisateur extends ORM\Entity {
  #[ORM\Id]
  private int $id;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $login;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $password;
  /** @var Role[] */
  #[ORM\ManyToMany(targetEntity: Role::class, mainEntity: self::class)]
  private array $roles;
  #[ORM\Column(type:ORM\ColumnType::BOOLEAN)]
  private bool $actif;
  #[ORM\Column(type:ORM\ColumnType::DATETIME, nullable: false, default: true)]
  private DateTime $creationDate;

  public function getId(): int { return $this->id; }
  public function getLogin(): string { return $this->login; }
  public function getPassword(): string { return $this->password; }
  /** @return Role[] */
  public function getRoles(): array { return $this->roles; }
  public function getActif(): bool { return $this->actif; }
  public function getCreationDate(): DateTime { return $this->creationDate; }
  
  public function setId(int $id): void { $this->id = $id; }
  public function setLogin(string $login): void { $this->login = $login; }
  public function setPassword(string $password): void { $this->password = $password; }
  /** @param Role[] $roles */
  public function setRoles(array $roles): void { $this->roles = $roles; }
  public function setActif(bool $actif): void { $this->actif = $actif; }
  public function setCreationDate(DateTime $creationDate): void { $this->creationDate = $creationDate; }
}