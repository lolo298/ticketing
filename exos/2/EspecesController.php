<?php

use animals\EspecesManager;
use animals\Especes;

$especesManager = new EspecesManager();

if (isset($_GET['action'])) {

    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $isDeleted = false;
        if ($especesManager->deleteEspece($_GET['id'])) {
            $isDeleted = true;
        }
    }
    if ($_GET['action'] === 'add') {
        $isAdded = false;
        $newEspece = new Especes($_POST);
        if ($especesManager->addEspece($newEspece)) {
            $isAdded = true;
        }
    }
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $isDeleted = false;
                if ($especesManager->deleteEspece($_GET['id'])) {
                    $isDeleted = true;
                }
            }
            break;
        case 'add':
            $isAdded = false;
            $newEspece = new Especes($_POST);
            if ($especesManager->addEspece($newEspece)) {
                $isAdded = true;
            }
            break;
        case 'update':
            $isUpdated = false;
            $espece = new Especes($_POST);
            if ($especesManager->updateEspece($espece)) {
                $isUpdated = true;
            }
            break;
    }
}



$listeEspeces = $especesManager->getAllEspece();


require_once $viewPath . 'EspecesView.php';
