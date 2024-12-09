<?php
namespace Ticketing\Models;
use ORM\Column;
use ORM\Id;
use ORM\ColumnType;
use ORM\Serializable;
use ORM\SerializableEntity;

#[Serializable(target: 'name')]
class State extends SerializableEntity {
  #[Id]
  private int $id;

  #[Column(type:ColumnType::VARCHAR)]
  private string $name;
  public function getId(): int { return $this->id; }
  public function getName(): string { return $this->name; }
  public function setId(int $id) { $this->id = $id; }
  public function setName(string $name) { $this->name = $name; }
}