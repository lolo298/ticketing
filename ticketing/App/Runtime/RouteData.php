<?php

namespace Runtime;

class RouteData {
  public string $name;
  public string $path;
  public string $http_method;
  public string $action;
  public object $controller;
}