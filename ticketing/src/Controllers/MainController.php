<?php
namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Runtime\BDD;
use Ticketing\Models\Ticket;

class MainController extends AbstractController {

  #[Route('/', 'GET', 'home')]
  public function home(): void {
    try{

      $bdd = BDD::getInstance();
    } catch (\PDOException $e) {
      echo 'Connexion échouée : ' . $e->getMessage();
      die();
    }
    
    $stmt = $bdd->prepare('SELECT * FROM ticket ORDER BY creation DESC LIMIT 10 OFFSET :offset');
    $stmt->bindValue(':offset', 0, \PDO::PARAM_INT);
    $stmt->execute();
    $tickets = $stmt->fetchAll();

    $ticket = new Ticket();
    // $ticket->setCreation(new \DateTime());
    // $ticket->setUpdate(new \DateTime());
    $ticket->setSubject("test orm");


    // $ticket->save();
    
    $this->render('home', ['tickets' => $tickets]);
  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    var_dump('login');
  }
  
}