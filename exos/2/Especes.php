<?php

namespace animals;


class Especes
{
    private int $id;
    private string $nom;


    public function __construct( array $data )
    {
        $this->hydrate( $data );
    }


    public function __toString()
    {
        return $this->getNom();
    }


    
    private function hydrate( array $data )  
    {
        foreach( $data as $key => $value ) {
            $method = 'set' . ucfirst( $key ); // setNom()
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


    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom( string $nom )
    {
        if( !is_string( $nom ) ) return false;
        $this->nom = $nom;
    }
}