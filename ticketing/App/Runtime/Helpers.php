<?php

namespace Runtime;

class Helpers {

  /** @var RouteData[] */
  private array $routes;

  private static $instance = null;

  static function getInstance(): self {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function __construct(array $routes = []) {
    $this->routes = $routes;

    if (self::$instance === null) {
      self::$instance = $this;
    }
  }

  function getPath(string $name): string {
    foreach ($this->routes as $r) {
      if ($r->name === $name) {
        return $r->path;
      }
    }

    return '';
  }
}