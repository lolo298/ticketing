<?php
namespace ORM;
use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class OneToMany {
  public string $targetEntity;
  public string $inversedBy;
  public function __construct(string $targetEntity, string $inversedBy) {
    $this->targetEntity = $targetEntity;
    $this->inversedBy = $inversedBy;
  }
}