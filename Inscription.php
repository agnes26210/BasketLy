<?php
// On démarre le systeme de session PHP
// Cela crée la variable $_SESSION et la rempli automatiquement avec les valeurs des sessions en cours
// Ces valeurs sont stocké par PHP, sur le serveur, et seront autodétruite après un certain temps ou via la page Logout.php
session_start();

include 'includes/bddConnect.php';

// Par defaut, on arrive sur la page sans qu'une inscription soit soumise
$inscriptionSoumise = false;
// On crée une variable $erreur qu'on initialise à "faux" car il n'y a pour l'instant pas d'erreur
$erreur = false;
// On vérifie que toutes les infos on bien été envoyés
if( isset($_POST['civ']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['naissance']) && isset($_POST['pers']) && isset($_POST['infosup']) && isset($_POST['nour'])) {
  // On nettoie tous les champs soumis avec trim() qui enlève les espaces au début et à la fin de chaque champs.
  $civ = trim( $_POST['civ'] );
  $nom = trim( $_POST['nom'] );
  $prenom = trim( $_POST['prenom'] );
  $dateNaissance = trim($_POST['naissance']);
  $nbPersonne = trim($_POST['pers']);
  $club = trim($_POST['infosup']);
  $aliment = trim($_POST['nour']);
  // si le champ est vide alors on lui donne comme valeur "null" (plus propre dans la BDD)
  empty($aliment) ? $aliment = null : null;
  
  // On vérifie que civ ne contient que des lettres et pas plus de 3 (MME étant le plus long possible)
  if( !preg_match('/^[a-z]{2,3}$/i', $civ)) {
    $erreur = true;
    $erreurCiv = true;
  }

  // On vérifie si nom ne contient pas de chiffre ou caratères spéciaux
  if( !preg_match('/^[- a-zàâäéèêëïîôöùûü\']{2,}$/i', $nom ) ) {
    // Si le nom contient des caractère interdit, on indique qu'il y a une erreur ($erreur = true) et on indique ou.
    $erreur = true;
    $erreurNom = true;
  }

  // On vérifie si prenom ne contient pas de chiffre ou caratères spéciaux
  if( !preg_match('/^[- a-zàâäéèêëïîôöùûü\']{2,}$/i', $prenom ) ) {
    // Si le nom contient des caractère interdit, on indique qu'il y a une erreur ($erreur = true) et on indique ou.
    $erreur = true;
    $erreurPrenom = true;
  }

  // On vérifie si date de naissance correct (JJ entre 01 et 31, MM entre 01 et 12, AAAA entre 1900 et 2999)
  if( !preg_match('/^(?:0[1-9]|[12][0-9]|3[01])\/(?:0[1-9]|1[012])\/(?:19|20)[0-9]{2}$/i', $dateNaissance) ) {
    $erreur = true;
    $erreurDateNaissance = true;
  }

  // On vérifie si nombre de personne correct
  if( !preg_match('/^[0-9]+$/i', $nbPersonne) || $nbPersonne > 10 ) {
    $erreur = true;
    $erreurNbPersonne = true;
  }

  // On vérifie si club est correct
  if( !preg_match('/^[\w\d\']+$/i', $club ) ){
    $erreur = true;
    $erreurClub = true;
  }

  // Si aucune erreur (donc $erreur === false) alors on envoie à la bdd
  if (!$erreur) {
    // On test si le combo nom / prénom soumis n'existe pas déjà dans la BDD 
    $queryDoublon = "SELECT * FROM inscription WHERE UPPER(nom) = UPPER(:nom) AND UPPER(prenom) = UPPER(:prenom)";
    $stmt = $db->prepare($queryDoublon);
    $stmt->execute(array('nom'=>$nom, 'prenom'=>$prenom));
    $result = $stmt->fetch();
    // Si aucun résultat alors c'est que le couple nom / prénom n'est pas déjà dans la BDD
    if (!$result) {
      // On peu alors insérer la nouvelle inscription
      $query = "INSERT INTO inscription (civilite, nom, prenom, datenaissance, accomp, club, aliment) VALUES (:civilite, :nom, :prenom, :datenaissance, :accomp, :club, :aliment)";
      $stmt = $db->prepare($query);
      $success = $stmt->execute(array(
        ':civilite'=>$civ,
        ':nom'=>$nom,
        ':prenom'=>$prenom,
        ':datenaissance'=>$dateNaissance,
        ':accomp'=>$nbPersonne,
        ':club'=>$club,
        ':aliment'=>$aliment
        )
      );
      // Si l'enregistrement à réussi
      if ($success) {
        // On modifie l'affichage via la variable $inscriptionSoumise
        $inscriptionSoumise = true;
      } else {
        // Sinon, on informe d'une erreur d'insertion dans la BDD
        $erreurBdd = true;
      }
    } else {
      // Sinon il y a un doublon, on ne l'enregistre pas et crée une variable d'erreur pour générer l'affiche à l'utilisateur
      $erreurDoublon = true;
    }
  }
} else if (isset($_POST['valider'])) {
  // Si tous les champs n'ont pas été envoyé mais que le bouton Valider à été cliquer, alors on affiche une erreur de remplissage
  $erreurRemplissage = true;
}

// On détruit la connection à la base de donnée (sécurité oblige !)
$db = null;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <?php
  $titre = "Inscription - Basketly";
  include "includes/header.php";
  ?>

  <!-- scripts -->
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
  <link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css" />

</head>

<body>

  <?php
  $active = "inscription";
  include "includes/navbar.php";
  ?>

  <div class="container-fluid">
    <section class="incri row">
      <header>
        <div class="col-xs-offset-3 col-xs-6">
          <h1 class="titre">Inscription à l'événement</h1>
          <h2 class="titre">Du samedi 5 décembre 2015</h2>
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
      else if (isset($erreurDoublon)) {
        ?>
        <div class="col-xs-offset-3 col-xs-6">
          <p>Il semblerait que ce couple nom/prénom soit déjà inscrit !</p>
          <p>Renseignez vous sur votre possible inscription par un tier.</p>
        </div>
        <?php
      }
      else if ($inscriptionSoumise === false) {
        ?>
        <div class="col-xs-offset-2 col-xs-8">
          <p class="merci">Merci de votre inscription nous vous donnons rendez-vous le Samedi 5 décembre 2015<br/>
            <h4 class="merci"><span class="rouge"><strong>(*) Champ obligatoire</strong></span></h4>
          </p>
        </div>
        <div class="col-xs-offset-4 col-xs-4">
          <form action="Inscription.php" method="post" class="inscri form-horizontal" id="form-inscription">

            <?php
            if(isset($erreurRemplissage)) {
              ?>
              <div class="erreur">
                <p>Veuillez remplir tous les champs obligatoires !</p>
              </div>
              <?php
            }
            ?>

            <div class="form-group">
              <p id="erreurCiv" class="erreur col-xs-offset-6" <?php echo (isset($erreurCiv) ? null : 'style="display:none;"' ) ?> >Civilité invalide ou non séléctionnée</p>
              <label for="civ" class="col-xs-6 control-label"><span class="import">*</span> Civilité :</label>
              <div class="radio col-xs-6">
                <label>
                  <input type="radio" name="civ" value="MR" <?php echo((isset($_POST['civ']) && $_POST['civ'] === "MR") ? 'checked' : null) ?>>MR
                </label>
                <label>
                  <input type="radio" name="civ" value="MME" <?php echo((isset($_POST['civ']) && $_POST['civ'] === "MME") ? 'checked' : null) ?>>MME
                </label>
              </div>
            </div>

            <div class="form-group">
              <p id="erreurNom" class="erreur col-xs-offset-6" <?php echo (isset($erreurNom) ? null : 'style="display:none;"' ) ?> >Nom invalide</p>
              <label for="nom" class="col-xs-6 control-label" data-toggle="tooltip" data-placement="top" title="Uniquement lettres accentuées, espaces, tirets ou apostrophes"><span class="underDotted"><span class="import">*</span> Nom<sup>?</sup> :</span></label>
              <div class="col-xs-6">
                <input class="form-control" type="text" name="nom" placeholder="Ex : NOM" value="<?php echo(isset($_POST['nom']) ? $_POST['nom'] : null) ?>">
              </div>
            </div>

            <div class="form-group">
              <p id="erreurPrenom" class="erreur col-xs-offset-6" <?php echo (isset($erreurPrenom) ? null : 'style="display:none;"' ) ?> >Prénom invalide</p>
              <label for="prenom" class="col-xs-6 control-label" data-toggle="tooltip" data-placement="top" title="Uniquement lettres accentuées, espaces, tirets ou apostrophes"><span class="underDotted"><span class="import">*</span> Prénom<sup>?</sup> :</span></label>
              <div class="col-xs-6">
                <input class="form-control" type="text" name="prenom" placeholder="Ex : PRENOM" value="<?php echo(isset($_POST['prenom']) ? $_POST['prenom'] : null) ?>">
              </div>
            </div>

            <div class="form-group">
              <p id="erreurDateNaissance" class="erreur col-xs-offset-6" <?php echo (isset($erreurDateNaissance) ? null : 'style="display:none;"' ) ?> >Date de naissance invalide</p>
              <label for="naissance" class="col-xs-6 control-label" data-toggle="tooltip" data-placement="top" title="Au format JJ/MM/AAAA"><span class="underDotted"><span class="import">*</span> Date de naissance (JJ/MM/AAAA)<sup>?</sup> :</span></label>
              <div class="col-xs-6">
                <!-- input type text et NON date car non supporté par firefox ! -->
                <input class="form-control" type="text" name="naissance" placeholder="Ex : JJ/MM/AAAA" id="datepicker" value="<?php echo(isset($_POST['naissance']) ? $_POST['naissance'] : null) ?>">
              </div>
            </div>

            <div class="form-group">
              <p id="erreurNbPersonne" class="erreur col-xs-offset-6" <?php echo (isset($erreurNbPersonne) ? null : 'style="display:none;"' ) ?> >Nombre invalide</p>
              <label for="pers" class="col-xs-6 control-label" data-toggle="tooltip" data-placement="top" title="Uniquement chiffres de 0 à 10"><span class="underDotted"><span class="import">*</span> Nombre accompagnants (max 10)<sup>?</sup> :</span></label>
              <div class="col-xs-6">
                <input class="form-control" type="number" name="pers" placeholder="Ex : 1, 2, 3, 4" value="<?php echo(isset($_POST['pers']) ? $_POST['pers'] : null) ?>">
              </div>
            </div>

            <div class="form-group">
              <p id="erreurClub" class="erreur col-xs-offset-6" <?php echo (isset($erreurClub) ? null : 'style="display:none;"' ) ?> >Club invalide ou non séléctionné</p>
              <label for="infosup" class="col-xs-6 control-label"><span class="import">*</span> Club de basket :</label>
              <div class="col-xs-6">
                <select class="form-control" name="infosup" id="infosup">
                  <option value="" <?php echo((isset($_POST['infosup'])) ? null : 'selected') ?> ></option>
                  <option value="Infosup1" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup1") ? 'selected' : null) ?> >Infosup1</option>
                  <option value="Infosup2" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup2") ? 'selected' : null) ?> >Infosup2</option>
                  <option value="Infosup3" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup3") ? 'selected' : null) ?> >Infosup3</option>
                  <option value="Infosup4" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup4") ? 'selected' : null) ?> >Infosup4</option>
                  <option value="Infosup5" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup5") ? 'selected' : null) ?> >Infosup5</option>
                  <option value="Infosup6" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup6") ? 'selected' : null) ?> >Infosup6</option>
                  <option value="Infosup7" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup7") ? 'selected' : null) ?> >Infosup7</option>
                  <option value="Infosup8" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup8") ? 'selected' : null) ?> >Infosup8</option>
                  <option value="Infosup9" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup9") ? 'selected' : null) ?> >Infosup9</option>
                  <option value="Infosup10" <?php echo((isset($_POST['infosup']) && $_POST['infosup'] === "Infosup10") ? 'selected' : null) ?> >Infosup10</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="nour" class="col-xs-6 control-label">Nourriture ou boisson que vous apportez :</label>
              <div class="col-xs-6">
                <textarea class="form-control" name="nour" id="ameliorer" placeholder="Nourriture ou boisson"><?php echo(isset($_POST['nour']) ? $_POST['nour'] : null) ?></textarea>
              </div>
            </div>

            <input type="submit" name="valider" value="Valider" class="btn btn-default">
          </form>
        </div>
        <?php
      }
      else {
        ?>
        <div class="col-xs-offset-3 col-xs-6">
          <div class="row">
            <div class="col-xs-12 text-center">
              <h2>Inscription Enregistrée</h2>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-6 text-right">
              <p>Civilité :</p>
              <p>Nom:</p>
              <p>Prenom:</p>
              <p>Date de naissance :</p>
              <p>Nombre de personnes accompagnantes :</p>
              <p>Quel est votre club de basket:</p>
              <p>Nourriture ou boisson que vous voulez emmener:</p>
            </div>
            <div class="col-xs-6 text-left">
              <p><?php echo $civ ?></p>
              <p><?php echo $nom ?></p>
              <p><?php echo $prenom ?></p>
              <p><?php echo $dateNaissance ?></p>
              <p><?php echo $nbPersonne ?></p>
              <p><?php echo $club ?></p>
              <p><?php echo $aliment ?></p>
            </div>
          </div>
          <div>

          </div>
          
        </div>
        <?php
      }

      ?>

    </section>
  </div>

  <?php
  include "includes/footer.php";
  ?>

  <!-- Bootstrap core JavaScript ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <script src="scripts/custom_script.js" type="text/javascript"></script>
  <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>