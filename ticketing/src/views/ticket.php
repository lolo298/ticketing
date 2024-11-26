<?php
/**
 * @var Ticketing\Models\Ticket $ticket
 */

 if ($ticket->getId() === null) {
  header("Location: /");
  die();
 }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="/public/css/style.css">
  <title>Ticket n°<?= $ticket->getId() ?></title>
</head>
<body>
  <nav class="navbar">
    <h1>Ticketing</h1>
    <ul>
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="contact.html">Contact</a></li>
    </ul>
  </nav>
  <main>
    <h2>Ticket n°<?= $ticket->getId() ?></h2>
    <p>Sujet: <?= $ticket->getSubject() ?></p>
    <p>Description: <?= $ticket->getDescription() ?></p>
    <p>Creation: <?= $ticket->getCreationDate()->format("d/M/Y H:m:s") ?></p>
    <p>Mise a jour: <?= $ticket->getUpdateDate()->format("d/M/Y H:m:s") ?></p>
    <p>Demandeur: <?= $ticket->getUtilisateur()->getLogin() ?></p>
    <p>Type: <?= $ticket->getType()->getName() ?></p>
    <p>Priorité: <?= $ticket->getPriority()->getName() ?></p>
    <p>Etat: <?= $ticket->getState()->getName() ?></p>
  </main>


  <script src="/public/js/index.js"></script>
</body>

</html>