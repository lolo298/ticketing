<?php

require 'autoload.php';

use animals\EspecesManager;
use animals\Especes;

$viewPath = 'view/';

if( isset( $_GET['controller'] ) ) {
    $controller = $_GET['controller'] . 'Controller.php';
    require_once $controller;
} else {
    require_once $viewPath . 'indexView.php';
}



?>





