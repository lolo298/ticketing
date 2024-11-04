<?php
namespace Runtime;

#[\Attribute]
class Route {
  private string $path;
  private string $method;

  private string $name;

  public function __construct(string $path, string $method, string $name) {
    $this->path = $path;
    $this->method = $method;
    $this->name = $name;
  }

  public function getPath(): string {
    return $this->path;
  }

  public function getMethod(): string {
    return $this->method;
  }

  public function getName(): string {
    return $this->name;
  }
}