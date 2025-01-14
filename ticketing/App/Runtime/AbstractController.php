<?php

namespace Runtime;

class AbstractController implements IAbstractController {
  private static ?\Twig\Environment $_twig = null;

  private static $VIEWS_PATH = __DIR__ . '/../../src/views';
  private static $MODELS_PATH = __DIR__ . '/../../src/models';

  protected static Session $session;

  public static function init(): void {
    self::initTwig();
    self::initSession();
  }

  public array $routes = [];

  public function render(string $view, array $data = []): void {

    try {
      $wrapper = self::$_twig->load("$view.html.twig");
    } catch (\Twig\Error\LoaderError $e) {
      echo "
      <h1>Erreur de chargement de la vue</h1>
      <p>La vue $view.html.twig n'a pas été trouvée</p>      
      ";
      http_response_code(404);
      return;
    }

    $data['session'] = self::$session;
    echo $wrapper->render($data);
  }

  private static function initSession(): void {
    self::$session = Session::getInstance();
  }

  private static function initTwig(): void {
    if (self::$_twig === null) {
      $loader = new \Twig\Loader\FilesystemLoader(self::$VIEWS_PATH);

      $namespaces = glob(self::$VIEWS_PATH . '/*', GLOB_ONLYDIR);
      foreach ($namespaces as $namespace) {
        if ($namespace === '.' || $namespace === '..') {
          continue;
        }
        $loader->addPath($namespace, basename($namespace));
      }


      self::$_twig = new \Twig\Environment($loader, [
        'debug' => true
      ]);
      self::$_twig->addExtension(new \Twig\Extension\DebugExtension());
      self::$_twig->addExtension(new TwigHelpers());
    }
  }
}
