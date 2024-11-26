<?php

namespace animals;


class Manager{

    protected string      $dsn = 'mysql:host=localhost;dbname=animals;charset=utf8';
    protected string      $login = 'root';
    protected string      $password = '';
    protected dbConnect   $manager;


    public function __construct()
    {
        $this->manager = dbConnect::getDb( $this->dsn, $this->login, $this->password );
    }

}