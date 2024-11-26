<?php 
namespace Runtime;
class AbstractController {

  private $VIEWS_PATH = __DIR__ . '/../../src/views';
  private $MODELS_PATH = __DIR__ . '/../../src/models';

  public array $routes = [];

  public function render(string $view, array $data = []): void {
    // global variables for view
    extract($data);

    extract([
      'helpers' => Helpers::getInstance()
    ]);

    require_once $this->VIEWS_PATH . '/' . $view . '.php';
  }
}