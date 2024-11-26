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
                <?php
                    if( isset( $isDeleted ) ) {
                        if( !$isDeleted ) {
                            echo '<div class="alert alert-danger" role="alert">Attention ! La suppression a échoué.</div>';
                        } else {
                            echo '<div class="alert alert-success" role="alert">Ligne supprimé</div>';
                        }
                    }
                    if( isset( $isAdded ) ) {
                        if( !$isAdded ) {
                            echo '<div class="alert alert-danger" role="alert">Attention ! L\'ajout a échoué.</div>';
                        } else {
                            echo '<div class="alert alert-success" role="alert">Espèce ajouté</div>';
                        }
                    }
                ?>

                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col"></th>    
                        <th scope="col">#</th>
                        <th scope="col">Nom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach( $listeEspeces as $espece ) {
                        ?>
                        <tr>
                            <td class="sm-col text-center"><a class="text-danger" href="?controller=Especes&action=delete&id=<?=$espece['id'];?>">X</a></td>
                            <td><?=$espece['id'];?></td>
                            <td><?=$espece['nom'];?></td>
                        </tr>

                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>    
        </div>    
        <form class="row" name="addEspeceForm" action="?controller=Especes&action=add" method="post">
            <div class="col-auto">
                <label class="form-label">Nouvelle espèce</label>
            </div>    
            <div class="col-auto">
                <input type="text" name="nom" value="" class="form-contro"/>
            </div>  
            <div class="col-auto">
                <input type="submit" value="Valider" name="valide" class="btn btn-primary"/>
            </div>    
        </form>         
    </main>

    <footer>
    </footer>    

</body>
</html>