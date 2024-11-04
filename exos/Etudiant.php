<?php
class Etudiant {
  private int $_etudiantId;
  private string $_nom;
  private DateTime $_naissance;
  public string $orientation;

  public function __construct($name, $orientation) {
    $this->_nom = $name;
    $this->orientation = $orientation;
  }
  public function getNom(): string {
    return $this->_nom;
  }

  public function setEtudiantId($id) {
    $this->_etudiantId = $id;
  }

  public function getEtudiantId(): int {
    return $this->_etudiantId;
  }

  public function getNaissance(): DateTime {
    return $this->_naissance;
  }

  public function setNaissance(DateTime $date) {
    $this->_naissance = $date;
  }





}
