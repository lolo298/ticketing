<?php
namespace Runtime;

class TwigHelpers extends \Twig\Extension\AbstractExtension {
  public function getFunctions()
  {
      return [
          new \Twig\TwigFunction('path', [$this, 'getPath']),
      ];
  }

  function getPath(string $name, $params = []): string {
    $routes = $GLOBALS['routes'];
    foreach ($routes as $r) {
      if ($r->name === $name) {
        $path = $r->path;
        foreach ($params as $k => $v) {

          $path = preg_replace("/\{$k\}/", (string)$v, $path);
        }
        return $path;
      }
    }

    return '';
  }
}