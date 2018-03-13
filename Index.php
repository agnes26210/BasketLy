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
  include "includes/header.php";
  ?>

</head>

<body>

  <?php
  $active = "accueil";
  include "includes/navbar.php";
  ?>

  <div class="container-fluid">

    <div class="row">

      <div class="imglrb">
        <img src="img/lrb.png" alt="Photo du club" />
      </div>

      <section>
        <h1 class="bienv"><strong><em>Bienvenue</em></strong></h1>

        <h3 class="lead">
          <p>La Ligue Régionale du Lyonnais de Basketball vous souhaiter la bienvenue.<br/>
            Le prochain événement est le 5 décembre 2015.<br/><br/>
            <a href="inscription.php"><strong><button type="button" class="btn btn-primary">S'inscrire</button></strong></a><br/><br/>
            <a href="contact.php"><strong><button type="button" class="btn btn-primary">Nous Contacter</button></strong></a><br/><br/>
          </p>
        </h3>
      </section>
    </div>

  </div><!-- /.container -->

  <?php
  include "includes/footer.php";
  ?>
</body>

</html>