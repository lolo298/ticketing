<?php
namespace ORM;
use \Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column {
  public ColumnType $type;
  public bool $nullable;
  public bool $default;
  public function __construct(ColumnType $type, bool $nullable = false, bool $default = false) {
    $this->type = $type;
    $this->nullable = $nullable;
    $this->default = $default;
  }
}