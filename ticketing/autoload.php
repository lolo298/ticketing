<?php
require_once __DIR__ . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
  $class = str_replace('\\', '/', $class);

  if (file_exists(__DIR__ .'/App/'. $class .'.php')) {
    require_once __DIR__ .'/App/'. $class .'.php';
  } else {
    $class = str_replace('Ticketing/', '', $class);
    $path = find_file(__DIR__ , $class . '.php');
    if ($path !== null && file_exists($path)) {
      require_once $path;
  }
}
});
function find_file($dir, $target): string|null {
  $files = scandir($dir);
  foreach ($files as $file) {
    if ($file === '.' || $file === '..') {
      continue;
    }
    if (is_dir($dir . '/' . $file)) {
      $path = find_file($dir .'/'. $file, $target);
      if ($path !== null) {
        return $path;

      }
    } else {
      if (str_contains("$dir/$file", $target)) {
        return $dir . "/". $file;
      }
    }
  }
  return null;
}