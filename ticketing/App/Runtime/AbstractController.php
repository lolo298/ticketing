<?php 
namespace Runtime;
class AbstractController {

  private $VIEWS_PATH = __DIR__ . '/../../src/views';
  private $MODELS_PATH = __DIR__ . '/../../src/models';

  public function render(string $view, array $data = []): void {
    extract($data);
    require_once $this->VIEWS_PATH . '/' . $view . '.php';
  }
}