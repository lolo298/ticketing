<?php
namespace Ticketing\Controllers;

use Runtime\AbstractController;
use Runtime\Route;
use Runtime\BDD;
use Ticketing\Models\Ticket;
use Ticketing\Repositories\UtilisateurManager;

class MainController extends AbstractController {

  private UtilisateurManager $userManager;

  public function __construct(UtilisateurManager $utilisateurManager) {
    $this->userManager = $utilisateurManager;
  }



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
    $ticket->setCreation(new \DateTime());
    $ticket->setUpdate(new \DateTime());
    $ticket->setSubject("test orm");

    $user = $this->userManager->getUser(1);
    // $ticket->setUtilisateur($user);

    echo "AAAAAAAAAAAAAA <br>";
    echo "<pre>";
    print_r($user->getRoles()[0]->getUtilisateurs()[0]);
    echo "</pre>";

    // $ticket->save();
    
    $this->render('home', ['tickets' => $tickets]);
  }

  #[Route('/login', 'GET', 'login')]
  public function login(): void {
    var_dump('login');
  }
  
}