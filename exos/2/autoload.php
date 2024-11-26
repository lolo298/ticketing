<?php

function chargerClasse($classe)
{

    $classe .= '.php';

    $temp = explode( '\\', $classe );
    $classe =  $temp[1];

    if( file_exists( $classe ) ) {
        require_once $classe;
    } else {
        header("HTTP/1.0 404 Not Found");
        die( "Error 404 : File <b>$classe</b> not found!" );
    }
}

spl_autoload_register('chargerClasse');