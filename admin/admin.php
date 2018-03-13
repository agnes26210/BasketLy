<?php
// On démarre le systeme de session PHP
// Cela crée la variable $_SESSION et la rempli automatiquement avec les valeurs des sessions en cours
// Ces valeurs sont stocké par PHP, sur le serveur, et seront autodétruite après un certain temps ou via la page Logout.php
session_start();

// Si il n'existe pas d'utilisateur authentifié
if (empty($_SESSION['user'])) {
  // Alors on le redirige vers l'accueil
  header('Location: ../index.php');
  // Et on stop l'exécution de ce fichier php
  die();
}

include '../includes/bddConnect.php';

// Avant tout, si on demande la suppression d'une inscription, on l'effectue
if (isset($_GET['delInscri'])) {
  $queryDel = 'DELETE FROM inscription WHERE id = :ID';
  $stmt = $db->prepare($queryDel);
  // On associe la variable un peu différement car on doit forcer son type en Integer (car stocké en tant qu'Integer dans la BDD)
  $stmt->bindParam(':ID', $_GET['delInscri'], PDO::PARAM_INT);
  $stmt->execute();
}

// Puis on génère l'affichage paginé
$inscriptionParPage = 10; //Nous allons afficher 10 inscriptions par page.

$queryTotalCount = 'SELECT COUNT(*) AS total FROM inscription';
$stmt = $db->prepare($queryTotalCount);

$countTotal = $stmt->execute(); //Nous récupérons le contenu de la requête dans $countTotal
$dataTotal = $stmt->fetch(PDO::FETCH_ASSOC); //On range retour sous la forme d'un tableau.
$total = $dataTotal['total']; //On récupère le total pour le placer dans la variable $total.

//Nous allons maintenant compter le nombre de pages.
$nombreDePages=ceil($total/$inscriptionParPage);

if(isset($_GET['page'])) // Si la variable $_GET['page'] existe...
{
  $pageActuelle = intval($_GET['page']);
  if($pageActuelle > $nombreDePages) { // Si la valeur de $pageActuelle (le numéro de la page) est plus grande que $nombreDePages...
    $pageActuelle = $nombreDePages;
  }
} else { // Sinon on commence par la première page
  $pageActuelle=1; // La page actuelle est la n°1    
}

$premiereEntree = ($pageActuelle - 1) * $inscriptionParPage; // On calcul la première entrée à lire

// La requête sql pour récupérer les messages de la page actuelle.
$queryInscriptions = "SELECT * FROM inscription ORDER BY id DESC LIMIT :premier, :parpage";
$stmt = $db->prepare($queryInscriptions);
// On associe les variables un peu différement car on doit forcer leurs type en Integer (à cause du "LIMIT" qui est une fonction SQL nécessitant des entiers)
$stmt->bindParam(':premier', $premiereEntree, PDO::PARAM_INT);
$stmt->bindParam(':parpage', $inscriptionParPage, PDO::PARAM_INT);
$stmt->execute();
// On récupère toutes les données sous forme d'un tableau clé => valeur avec clé = nom de la colonne | valeur = valeur
$dataInscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// On ferme la connection avec la BDD
$db = null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Administration - BasketLy</title>

  <!-- Bootstrap core CSS -->
  <link href="../bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="../style.css" type="text/css" />

  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>

  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="../index.php">Basketly</a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="../Index.php">Accueil</a></li>
          <li><a href="../Inscription.php">Inscription</a></li>
          <li><a href="../Contact.php">Contact</a></li>
          <li class="active"><a href="../login.php">Administration</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <?php
          // Si il existe une variable de session et qu'une session avec un utilisateur est en cours
          if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
            // Alors on affiche le bouton de déconnexion
            ?>
            <li class="pull-right"><a href="../logout.php">Déconnexion</a></li>
            <?php
          }
          ?>
        </ul>
      </div><!--/.nav-collapse -->
    </div>
  </div>

  <div class="container-fluid">
    <section class="row">
      <header>
        <div class="col-xs-offset-2 col-xs-8">
          <h1 class="bienv"><strong><em>Administration</em></strong></h1>
        </div>
        <div class="clearfix"></div>
      </header>

      <div class="col-xs-offset-2 col-xs-8">
        <?php
        // Si il y des données à afficher
        if ($dataInscriptions) {
          // Alors on prépare le tableau
          ?>
          <table class="table table-striped table-bordered">
            <thead>
              <th>Civilité</th>
              <th>Nom</th>
              <th>Prenom</th>
              <th>Date Naissance</th>
              <th>Nombre accompagnants</th>
              <th>Club</th>
              <th>Aliments</th>
              <th>Suppression</th>
            </thead>
            <tbody>
              <?php
              // Et pour chaque donnée on crée une nouvelle ligne dans notre tableau
              foreach ($dataInscriptions as $row => $value) {
                echo '
                <tr>
                  <td><strong>'.$value['civilite'].'</strong></td>
                  <td><strong>'.$value['nom'].'</strong></td>
                  <td>'.$value['prenom'].'</td>
                  <td><strong>'.$value['datenaissance'].'</strong></td>
                  <td>'.$value['accomp'].'</td>
                  <td><strong>'.$value['club'].'</strong></td>
                  <td>'.$value['aliment'].'</td>
                  <td><a href="admin.php?delInscri='.$value['id'].'"><button class="btn btn-danger">Supprimer</button></a></td>
                </tr>';   
              }
              ?>
            </tbody>
          </table>
          <?php

          echo '<p align="center">Page : '; //Pour l'affichage, on centre la liste des pages
          for($i=1; $i<=$nombreDePages; $i++) {//On fait notre boucle
            if($i==$pageActuelle) { //Si il s'agit de la page actuelle...
            echo ' [ '.$i.' ] '; 
          } 
            else { //Sinon...
              echo ' <a href="admin.php?page='.$i.'">'.$i.'</a> ';
            }
          }
          echo '</p>';
        } else {
          // Sinon, il n'y a aucune donnée à afficher, on informe simplement l'administrateur
          echo '<p class="lead">Aucune inscriptions enregistrée pour le moment !</p>';
        }
        ?>
      </div>
    </section>


  </div><!-- /.container -->

  <?php
  include "../includes/footer.php";
  ?>
</body>

</html>