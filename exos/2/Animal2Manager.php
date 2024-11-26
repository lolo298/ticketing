<?php

namespace animals;

class Animal2Manager extends Manager
{

    public function __construct()
    {
        parent::__construct();
    }



    public function getAllAnimaux()
    {
        $sql = 'SELECT 
                    animal2.id, 
                    animal2.sexe, 
                    animal2.nom, 
                    DATE_FORMAT(animal2.date_naissance, "%d/%m/%Y") AS date_naissance, 
                    especes.nom AS nom_espece
                FROM animal2 
                LEFT JOIN especes ON especes.id = animal2.id_espece
                ORDER BY animal2.nom ASC';
        $reponse = $this->manager->bdd->query( $sql );

        $listAnimaux = $reponse->fetchAll( \PDO::FETCH_ASSOC );
        $reponse->closeCursor();
        return $listAnimaux;
    }



    public function getAnimal( int $id )
    {
        $sql = 'SELECT * FROM animal2 WHERE id=:id';
        $reponse = $this->manager->bdd->prepare( $sql );
        $reponse->execute( ['id'=>$id ] );

        $animal = $reponse->fetch( PDO::FETCH_ASSOC );
        $reponse->closeCursor();
        return $animal;

    }


    public function addAnimal()
    {

    }

    public function updateAnimal( int $id )
    {

    }

    public function deleteAnimal( int $id )
    {

    }

}