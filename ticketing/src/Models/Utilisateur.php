<?php
namespace Ticketing\Models;

use \DateTime;

class Utilisateur {
  private int $id;
  private string $login;
  private string $password;
  private $role;
  private bool $actif;
  private DateTime $date;
}