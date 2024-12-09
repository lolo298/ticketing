<?php
namespace Ticketing\Models;

use ORM;

#[ORM\Serializable(target: 'name')]
class Priority extends ORM\SerializableEntity {
  #[ORM\Id]
  private int $id;
  #[ORM\Column(type:ORM\ColumnType::VARCHAR)]
  private string $name;
  public function getId(): int { return $this->id; }
  public function getName(): string { return $this->name; }
  public function setId(int $id) { $this->id = $id; }
  public function setName(string $name) { $this->name = $name; }
}