<?php

use animals\Animal2Manager;

$animalManager = new Animal2Manager();


$listeAnimaux = $animalManager->getAllAnimaux();

require_once $viewPath . 'Animal2View.php';