<?php
namespace Ticketing\Models;
use ORM;

#[ORM\Serializable(target: 'name')]
class Role extends ORM\SerializableEntity {
  #[ORM\Id]
  private int $id;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $name;

  /** @var Utilisateur[] */
  #[ORM\ManyToMany(targetEntity: self::class, mainEntity: Utilisateur::class)]
  private array $utilisateurs;
  public function getId(): int { return $this->id; }
  public function getName(): string { return $this->name; }
  public function getUtilisateurs(): array { return $this->utilisateurs; }
  public function setId(int $id) { $this->id = $id; }
  public function setName(string $name) { $this->name = $name; }
  public function setUtilisateurs(array $utilisateurs) { $this->utilisateurs = $utilisateurs; }
}