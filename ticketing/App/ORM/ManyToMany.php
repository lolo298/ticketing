<?php
namespace ORM;
use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ManyToMany {
  public string $targetEntity;
  public string $mainEntity;
  public function __construct(string $targetEntity, string $mainEntity) {
    $this->targetEntity = $targetEntity;
    $this->mainEntity = $mainEntity;
  }
  
}