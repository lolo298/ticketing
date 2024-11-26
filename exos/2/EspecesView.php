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
                <h2>Liste des espèces</h2>
            </div>     
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach( $listeEspeces as $espece ) {
                        ?>
                        <tr>
                            <form action="index.php?action=update" method="post">
                                <td><?=$espece['id'];?></td>
                                <td><input type="text" name="nom" value="<?=$espece['nom'];?>"></td>
                                <td><button type="submit" class="btn btn-primary">Modifier</button></td>
                            </form>
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