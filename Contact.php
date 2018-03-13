<?php
// On démarre le systeme de session PHP
// Cela crée la variable $_SESSION et la rempli automatiquement avec les valeurs des sessions en cours
// Ces valeurs sont stocké par PHP, sur le serveur, et seront autodétruite après un certain temps ou via la page Logout.php
session_start();

?>
<!DOCTYPE html>

<html lang="fr">
<head>
  <?php
  $titre = "Contact - Basketly";
  include "includes/header.php";
  ?>
</head>

<body>

  <?php
  $active = "contact";
  include "includes/navbar.php";
  ?>

  
  <div class="container-fluid">
    <div class="imglrbc">
      <img src="img/lrbcontact.png" alt="Photo du club" />
    </div>
    <section class="row">
        <header class="row">
          <div class="col-xs-offset-3 col-xs-6">
            <em><strong><h1 class="contact">Nous contacter</h1></strong></em>
          </div>
          <div class="clearfix"></div>
        </header>
        <div class="row">
          <div class="col-xs-offset-2 col-xs-8">
            <p class="lead">
              <strong><em>> Adresse :</em></strong> Atrium 3, 1-3 rue du colonel Chambonnet 69500 BRON
            </p>
            <p class="lead">
              <strong><em>> Tél :</em></strong> 04 72 57 85 20
            </p>
            <p class="lead">
              <strong><em>> Email :</em></strong> secretariat@basketly.com
            </p>
            
          </div>
        </div>
    </section>

  </div><!-- /.container -->
  <?php
  include "includes/footer.php";
  ?>
</body>



</html>