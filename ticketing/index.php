<?php

use Runtime\Route;
require_once __DIR__ . '/autoload.php';

//scan existing controllers
function scan_dir($dir) {
  $files = scandir($dir);
  $controllers = [];
  foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
      continue;
    }
    if (is_dir($dir . '/' . $file)) {
      $controllers = array_merge($controllers, scan_dir($dir . '/' . $file));
    } else {
      $controller = str_replace('.php', '', $file);
      $controllers[] = $controller;
    }
  }
  return $controllers;
}

$controllers = scan_dir(__DIR__ . "/src/Controllers");
$routes = [];

class RouteData {
  public string $name;
  public string $path;
  public string $http_method;
  public string $action;
  public object $controller;
}

//parse routes availables
foreach($controllers as $controllerName) {
  $controllerName = "Ticketing\\Controllers\\" . $controllerName;


  $controllerReflect = new \ReflectionClass($controllerName);
  $constructParams = $controllerReflect->getConstructor()->getParameters();
  $params = [];
  foreach ($constructParams as $constructParam) {
    $type = $constructParam->getType();
    if ($type !== null) {
      $className = $type->getName();
      $instance = new $className();
      $params[$constructParam->getName()] = $instance;
    }
  }

  $controller = $controllerReflect->newInstanceArgs($params);



  // $controller = new $controllerName();
  $reflect = new ReflectionClass( $controller);
  $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);
  foreach ($methods as $method) {
    $attrs = $method->getAttributes(Route::class);
    if (count($attrs) > 0) {
        $route = $attrs[0]->newInstance();
        $name = $route->getName();
        $path = $route->getPath();
        $http_method = $route->getMethod();

        $data = new RouteData();
        $data->name = $name;
        $data->path = $path;
        $data->http_method = $http_method;
        $data->action = $method->getName();
        $data->controller = $controller;

        $routes[$path."::".$http_method] = $data;
    }
  }
  
}



$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$curr = $routes["$request::$method"] ?? null;
if ($curr === null) {
  echo '404';
} else {
  $action = $curr->action;
  $controller = $curr->controller;
  $controller->$action();
}

