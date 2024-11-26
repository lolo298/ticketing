<?php

namespace animals;

use animals\Especes;

class EspecesManager extends Manager
{

    public function __construct()
    {
        parent::__construct();

    }


    /**
     * 
     */
    public function getAllEspece()
    {
        $sql = 'SELECT * FROM especes';
        $reponse = $this->manager->bdd->query( $sql );

        $listEspeces = $reponse->fetchAll( \PDO::FETCH_ASSOC );
        $reponse->closeCursor();
        return $listEspeces;
    }


    /**
     * 
     */
    public function getEspece( int $id )
    {
        $sql = 'SELECT * FROM especes WHERE id=:id';
        $reponse = $this->manager->bdd->prepare( $sql );
        $reponse->execute( ['id'=>$id ] );

        $espece = $reponse->fetch( \PDO::FETCH_ASSOC );
        $reponse->closeCursor();
        return $espece;
    }


    /**
     * 
     */
    public function addEspece( Especes $espece )
    {
        if( is_object( $espece ) ) {
            $sql = 'INSERT INTO especes(nom) VALUE (:nom)';
            $reponse = $this->manager->bdd->prepare( $sql );
            $state = $reponse->execute( ['nom'=>$espece->getNom() ] );

            return $state;

        } else return false;
    }



    public function updateEspece( Especes $espece )
    {
        if( is_object( $espece ) ) {
        $sql = 'UPDATE especes SET nom=:nom WHERE id=:id';
        $reponse = $this->manager->bdd->prepare( $sql );
        $state = $reponse->execute( ['id'=>$espece->getId(), 'nom'=>$espece->getNom() ] );
        return $state;
        } else return false;

    }

    public function deleteEspece( int $id )
    {
        if( !empty( $id ) && is_int( $id ) ) {
            $sql = 'DELETE FROM especes WHERE id=:id';
            $reponse = $this->manager->bdd->prepare( $sql );
            $reponse->execute( ['id'=>(int)$id ] );

            return $reponse->rowCount();
        } else {
            return false;
        }
    }

}