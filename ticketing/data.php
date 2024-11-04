<?php
require_once 'BDD.php';
$bdd = BDD::getInstance();

$bdd->query('INSERT INTO tickets (id_utilisateur, subject, description) VALUES (1, "test", "test")');