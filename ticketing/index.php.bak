<?php
require_once 'BDD.php';
try{

  $bdd = BDD::getInstance();
} catch (PDOException $e) {
  echo 'Connexion échouée : ' . $e->getMessage();
  die();
}

$stmt = $bdd->prepare('SELECT * FROM tickets ORDER BY creation DESC LIMIT 10 OFFSET :offset');
$stmt->bindValue(':offset', 0, PDO::PARAM_INT);
$stmt->execute();
$tickets = $stmt->fetchAll();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <link rel="stylesheet" href="/public/css/style.css">
  <title>Document</title>
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
    <button id="newTicketBtn">nouveau ticket</button>
    <table>
      <tr>
        <th>N°</th>
        <th>Sujet</th>
        <th>Creation</th>
        <th>Mise a jour</th>
        <th>Demande</th>
        <th>Etat</th>
        <th></th>
      </tr>
      
      <?php foreach ($tickets as $ticket): ?>
        <tr>
          <td><?= $ticket['id_ticket'] ?></td>
          <td><?= $ticket['subject'] ?></td>
          <td><?= $ticket['creation'] ?></td>
          <td><?= $ticket['update'] ?></td>
          <td><?= $ticket['description'] ?></td>
          <td><?= null ?></td>
          <td><a href="ticket.php?id=<?= $ticket['id_ticket'] ?>"><i class="fas fa-eye"></i></a></td>
        </tr>
      <?php endforeach; ?>
    </table>

  </main>


  <dialog id="newTicketModal">
    <form method="POST" action="/api/newTicket.php">
      
      <label for="subject">Sujet</label>
      <input type="text" name="subject" id="subject">
      
      <label for="description">Description</label>
      <textarea name="description" id="description" cols="30" rows="10"></textarea>
      
      <button type="submit">Envoyer</button>
      <input type="reset"/>
    </form>
  </dialog>



  <script src="/public/js/index.js"></script>
</body>
</html>