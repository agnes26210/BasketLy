<?php 
// On démarre le systeme de session PHP
// Cela crée la variable $_SESSION et la rempli automatiquement avec les valeurs des sessions en cours
// Ces valeurs sont stocké par PHP, sur le serveur, et seront autodétruite après un certain temps ou via la page Logout.php
session_start();

// Suppression des variables de session et de la session
$_SESSION = array();
// On détruit la session
session_destroy();

// Et on redirige vers la page de Login (choix arbitraire, on peu rediriger ou on veux)
header('Location: login.php');
die();