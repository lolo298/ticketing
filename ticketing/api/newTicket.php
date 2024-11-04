<?php
require_once '../BDD.php';

$bdd = BDD::getInstance();

$stmt = $bdd->prepare('INSERT INTO tickets (id_utilisateur, subject, description) VALUES (1, :subject, :description)');
$stmt->bindValue(':subject', $_POST['subject']);
$stmt->bindValue(':description', $_POST['description']);

$stmt->execute();

header('Location: '. $_SERVER['HTTP_REFERER']);