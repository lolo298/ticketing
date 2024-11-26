<?php

use Runtime\Helpers;
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

        $data = new \Runtime\RouteData();
        $data->name = $name;
        $data->path = $path;
        $data->http_method = $http_method;
        $data->action = $method->getName();
        $data->controller = $controller;
        $regexPath = preg_replace('/\{(.+)\}/', '(?<$1>(\w+))', $path);
        $regexPath = str_replace('/', '\/', $regexPath);
        $regexPath = '/^' . $regexPath . '$/';
        $routes[$regexPath."::".$http_method] = $data;
    }
  }
  
}



$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
$curr = null;
$vals = [];

foreach ($routes as $path => $route) {
  $r = explode("::", $path)[0];
  $matches = [];
  if (preg_match($r, $request, $matches) && $method === $route->http_method) {
    $curr = $route;
    foreach ($matches as $key => $match) {
      if (str_contains($route->path, "{$key}")) {
        $vals[$key] = $match;
      }
    }
    

    break;
  }
}



if ($curr === null) {
  echo '404';
} else {
  $helpers = new Helpers($routes);

  $action = $curr->action;
  $controller = $curr->controller;
  $controller->$action($vals);
}

