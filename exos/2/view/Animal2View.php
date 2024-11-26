<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/default.css">
    <title>Animals</title>
</head>
<body>
    <?php
        require_once '_header.php';
    ?>

    <main class="container">
        <div class="row">
            <div class="col-12">
                <h2>Liste des animaux</h2>
            </div>     
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Sexe</th>
                        <th scope="col">Espece</th>
                        <th scope="col">Date naissance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach( $listeAnimaux as $animal ) {
                        ?>
                        <tr>
                            <td><?=$animal['id'];?></td>
                            <td><?=$animal['nom'];?></td>
                            <td><?=$animal['sexe'];?></td>
                            <td><?=$animal['nom_espece'];?></td>
                            <td><?=$animal['date_naissance'];?></td>
                        </tr>

                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>    
        </div>    
    </main>

    <footer>
    </footer>    

</body>
</html>