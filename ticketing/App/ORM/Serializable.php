<?php
namespace ORM;

use \Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Serializable {

  public string $target;

  public function __construct(string $target) {
    $this->target = $target;
  }

  
}