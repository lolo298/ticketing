<?php

class Animal2
{

    private int         $id;
    private int         $id_espece;
    private string      $sexe;
    private Datetime    $date_naissance;
    private string      $commentaires;
    private string      $nom;


    public function __construct()
    {

    }


    public function hydrate( array $data )  
    {
        foreach( $data as $key => $value ) {
            $method = 'set' . ucfirst( $key );
            if( method_exists($this, $method ) ) {
                $this->$method($value);
            }
        }
    }
    

    public function getId(): int
    {
        return $this->id;
    }

    public function setId( int $id )
    {
        $this->id = $id;
    }


    public function getIdEspece(): int
    {
        return $this->id_espece;
    }
    public function setIdEspece( int $idEspece )
    {
        $this->id_espece = $idEspece;
    }


    public function getSexe(): string
    {
        return $this->sexe;
    }
    public function setSexe( string $sexe )
    {
        $this->sexe = $sexe;
    }


    public function getDateNaissance(): Datetime
    {
        return $this->date_naissance;
    }
    public function setDateNaissance( Datetime $datenaiss )
    {
        $this->date_naissance = $datenaiss;
    }


    public function getCommentaire(): string
    {
        return $this->commentaires;
    }
    public function setCommentaire( string $commentaires )
    {
        $this->commentaires = $commentaires;
    }


    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom( string $nom )
    {
        $this->nom = $nom;
    }

}