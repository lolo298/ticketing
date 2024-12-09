<?php

namespace ORM;

abstract class SerializableEntity extends Entity {

  public function __tostring() {
    $reflection = new \ReflectionClass($this);
    $attributes = $reflection->getAttributes();

    if (count($attributes) === 0) {
      throw new \Exception('No serializable attribute found');
    }

    $attribute = $attributes[0];
    $target = $attribute->newInstance()->target;

    $method = $reflection->getMethod('get' . ucfirst($target));
    $value = $method->invoke($this);
    return $value;
  }
}
