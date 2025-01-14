<?php

namespace Runtime;

interface Singleton {
  public static function getInstance(): Singleton;
}
