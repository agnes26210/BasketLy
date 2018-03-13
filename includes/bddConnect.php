<?php

try {
  // On se connect à la BDD
  $bddName = "basketly";
  $host = "localhost";
  $user = "root";
  $mdp = "";
  $db = new PDO('mysql:host='.$host.';dbname='.$bddName.';charset=UTF8', $user, $mdp);
} catch (PDOException $e) {
  // Si cela échoue, on affiche un erreur et stop l'affichage de la page, notre site est inutilisable de toute façon
  print "Erreur !: " . $e->getMessage();
  $db = null;
  die();
}
