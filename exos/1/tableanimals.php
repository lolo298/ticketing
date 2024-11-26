<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>

<body>
  <?php
  echo "yaaay";
  $bdd = new PDO('mysql:host=db;dbname=animals;charset=utf8', 'root', 'root_password');

  // $res = $bdd->prepare('SELECT a.*, e.nom AS nom_espece FROM animals a LEFT JOIN especes e ON a.id_espece = e.id');
  $res = $bdd->prepare('SELECT * FROM especes');
  $res->execute();
  $especes = $res->fetchAll(PDO::FETCH_ASSOC);
  ?>
  <pre>

  <?php
  // print_r($data);
  ?>
</pre>

  <form action="">
    <select name="espece">
      <?php
      foreach ($especes as $espece) {
        echo '<option value="' . $espece['id'] . '">' . $espece['nom'] . '</option>';
      }
      ?>
    </select>
    <button type="submit">Valider</button>
  </form>

  <table>
    <tr>
      <th>Nom</th>
      <th>Espece</th>
      <th>Sexe</th>
      <th>Date dnaissance</th>
      <th>Commentaire</th>
    </tr>
    <?php

    if (isset($_GET['espece'])) {
      $res = $bdd->prepare('SELECT a.*, e.nom AS nom_espece FROM animals a LEFT JOIN especes e ON a.id_espece = e.id WHERE a.id_espece = :id_espece');
      $res->execute(['id_espece' => $_GET['espece']]);
    } else {
      $res = $bdd->prepare('SELECT a.*, e.nom AS nom_espece FROM animals a LEFT JOIN especes e ON a.id_espece = e.id');
      $res->execute();
    }

    $data = $res->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as $animal) {
      echo '<tr>';
      echo '<td>' . $animal['nom'] . '</td>';
      echo '<td>' . $animal['nom_espece'] . '</td>';
      echo '<td>' . $animal['sexe'] . '</td>';
      echo '<td>' . $animal['date_naissance'] . '</td>';
      echo '<td>' . $animal['commentaires'] . '</td>';
      echo '</tr>';
    }



    ?>
  </table>


</body>

</html>