<?php

namespace Runtime;

interface IAbstractController {
  public static function init(): void;
  public function render(string $view, array $data = []): void;
}
