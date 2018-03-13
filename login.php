<?php 
// On démarre le systeme de session PHP
// Cela crée la variable $_SESSION et la rempli automatiquement avec les valeurs des sessions en cours
// Ces valeurs sont stocké par PHP, sur le serveur, et seront autodétruite après un certain temps ou via la page Logout.php
session_start();

// Si la variable de session "user" contient quelque chose, donc un utilisateur est connecté !
if(!empty($_SESSION['user'])) {
  // Alors on le redirige vers la page d'amdin
  header('Location: admin/admin.php');
  // On arrete l'exécution du script de cette page car plus rien à faire ici, on a redirigé
  die();
} else if(isset($_POST['valider'])) { // Sinon, Si on viens de soumettre le formulaire de connexion
  // On se connecte à la BDD
  include 'includes/bddConnect.php';

  // On récupère le login et le mot de passe que l'utilisateur viens de soumettre
  $user = $_POST['user'];
  // Hachage du mot de passe (sécurité)
  $pass_hache = sha1($_POST['pass']);

  // Vérification des identifiants
  $query = $db->prepare('SELECT id FROM accadmin WHERE user = :user AND pass = :pass');
  $query->execute(array(
    'user' => $user,
    'pass' => $pass_hache));

  // Fermeture de la connexion à la BDD
  $db = null;

  // $resultat prend pour valeur la ligne concernant l'utilisateur dans la base s'il existe, sinon rien
  $resultat = $query->fetch();
  // S'il n'y a PAS de correspondance avec un compte dans la base
  if (!$resultat) {
    // Alors on initialise une variable d'erreur concernant le login à True
    $erreurLogin = true;
  }
  else {
    // Sinon, on ajoute l'id de l'utilisateur et sont pseudo dans la variable $_SESSION 
    $_SESSION['id'] = $resultat['id'];
    $_SESSION['user'] = $user;
    // A partir de maintenant, une session concernant cette utilisateur est créée et sera détruit si on appel logout.php ou après un certain temps définie par php
    // L'utilisateur est connecté, on le redirige vers l'admin
    header('Location: admin/admin.php');
    // Et on s'arrete ici pour ce fichier
    die();
  }
}

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
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" href="style.css" type="text/css" />

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
        <a class="navbar-brand" href="#">Basketly</a>
      </div>
      <div id="navbar" class="collapse navbar-collapse">
       <ul class="nav navbar-nav">
         <li><a href="Index.php">Acceuil</a></li>
         <li><a href="Inscription.php">Inscription</a></li>
         <li><a href="Contact.php">Contact</a></li>
         <li class="active"><a href="login.php">Administration</a></li>
       </ul>
     </div><!--/.nav-collapse -->
   </div>
 </div>

 <div class="container-fluid">

  <section class="login row">
    <header>
      <div class="col-xs-offset-3 col-xs-6">
        <h1 class="titre">Login administration</h1>
      </div>
      <div style="clear:both"></div>
    </header>
    <?php 
    if (isset($erreurBdd)) { 
      ?>
      <div class="col-xs-offset-3 col-xs-6">
        <p>Erreur d'enregistrement dans la base de donnée !</p>
        <p>Veuillez contacter l'administrateur du site.</p>
      </div>
      <?php 
    }
    ?>
    <div class="col-xs-offset-4 col-xs-4">
    <form action="login.php" method="post" class="form-horizontal" id="form-login">
        <?php if(isset($erreurLogin)) { ?>
        <div class="erreur text-center">
          <p>Combinaison login/mot de passe incorrecte</p>
        </div>
        <?php } ?>

        <div class="form-group">
          <label for="user" class="col-xs-5 control-label">Mot de passe :</label>
          <div class="col-xs-7">
            <input class="form-control" type="text" name="user">
          </div>
        </div>

        <div class="form-group">
          <label for="pass" class="col-xs-5 control-label">Login :</label>
          <div class="col-xs-7">
            <input class="form-control" type="text" name="pass">
          </div>
        </div>

        <div class="form-group">
            <div class="col-xs-offset-5 col-xs-7">
              <input type="submit" name="valider" value="Valider" class="btn btn-default">
            </div>
        </div>
      </form>
    </div>
  </section>
</div>

<?php
include 'includes/footer.php';
?>
</body>

</html>