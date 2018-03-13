<div class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="index.php">Basketly</a>
    </div>
    <div id="navbar" class="collapse navbar-collapse">
      <ul class="nav navbar-nav">
        <li <?php echo(($active === "accueil") ? 'class="active"' : null )?> ><a href="Index.php">Accueil</a></li>
        <li <?php echo(($active === "inscription") ? 'class="active"' : null )?> ><a href="Inscription.php">Inscription</a></li>
        <li <?php echo(($active === "contact") ? 'class="active"' : null )?> ><a href="Contact.php">Contact</a></li>
        <li <?php echo(($active === "admin") ? 'class="active"' : null )?> ><a href="login.php">Administration</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <?php 
        if(isset($_SESSION['user']) && !empty($_SESSION['user'])) {
          ?>
          <li><a href="logout.php">Déconnexion</a></li>
          <?php
        }
        ?>
      </ul>
    </div><!--/.nav-collapse -->
  </div>
</div>