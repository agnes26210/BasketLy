
$.datepicker.regional.fr = {
  closeText: 'Fermer',
  prevText: 'Précédent',
  nextText: 'Suivant',
  currentText: 'Aujourd\'hui',
  monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
  monthNamesShort: ['Janv.','Févr.','Mars','Avril','Mai','Juin','Juil.','Août','Sept.','Oct.','Nov.','Déc.'],
  dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
  dayNamesShort: ['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'],
  dayNamesMin: ['D','L','M','M','J','V','S'],
  weekHeader: 'Sem.',
  dateFormat: 'dd/mm/yy',
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: false,
  yearSuffix: ''
};
$.datepicker.setDefaults($.datepicker.regional.fr);

jQuery(document).ready(function($){
  // On initialise le selectionneur de date
  $("#datepicker").datepicker({
    // On permet de changer le mois et l'année via un menu déroulant.
    changeMonth: true,
    changeYear: true
  });

  // On initialise les infos-bulle
  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });


  // On detecte la soumission du formulaire (on submit => à la soumission)
  // La fonction prend un paramètre "event" correspondant à l'évenement déclenché par la soumission du formulaire
  $('#form-inscription').on('submit', function(event){
    // on créer une variable "erreur" qu'on initialise à faux (il n'y a actuellement pas d'erreur)
    var erreur = false;
    // on récupère tous les champs du formulaire
    var formInputs = $(this).serializeArray();
    // on crée un objet vide
    var inputs = {};
    // et on le rempli avec chaqu'un des champs de notre formulaire sous la forme : [clé = name du champ] => [valeur = valeur du champ]
    $.each(formInputs, function(index, value) {
      inputs[value.name] = value.value;
    });
    // On construit une expression régulière correspondant à ce qu'on veut valider (ici que la civilité soit composé d'aux moins 2 lettres de l'alphabet)
    var regex = /^[a-z]{2,}$/i;
    // Si le champ civ existe et qu'il est valide suivant l'expression régulière précédante, alors :
    if(inputs.civ && regex.test(inputs.civ)) {
      // On efface le message d'erreur s'il été présent
      $('#erreurCiv').fadeOut();
    } else {
      // Sinon, on affiche l'erreur et on met notre variable "erreur" à vrai
      $('#erreurCiv').fadeIn();
      erreur = true;
    }
    // Ainsi de suite pour tous les champs
    regex = /^[- a-zàâäéèêëïîôöùûü\']{2,}$/i;
    if(inputs.nom && regex.test(inputs.nom)) {
      $('#erreurNom').fadeOut();
    } else {
      $('#erreurNom').fadeIn();
      erreur = true;
    }
    // Ici on ne change pas la variable "regex" car on utilise la même regex que pour le nom
    if(inputs.prenom && regex.test(inputs.prenom)) {
      $('#erreurPrenom').fadeOut();
    } else {
      $('#erreurPrenom').fadeIn();
      erreur = true;
    }
    regex = /^(?:0[1-9]|[12][0-9]|3[01])\/(?:0[1-9]|1[012])\/(?:19|20)[0-9]{2}$/i;
    if(inputs.naissance && regex.test(inputs.naissance)) {
      $('#erreurDateNaissance').fadeOut();
    } else {
      $('#erreurDateNaissance').fadeIn();
      erreur = true;
    }
    regex = /^[0-9]+$/i;
    // Ici on s'assure en plus que le nombre de personne est au maximum de 10 (inferieur à 11)
    if(inputs.pers && regex.test(inputs.pers) && inputs.pers < 11) {
      $('#erreurNbPersonne').fadeOut();
    } else {
      $('#erreurNbPersonne').fadeIn();
      erreur = true;
    }
    regex = /^[\w\d\']+$/i;
    if(inputs.infosup && regex.test(inputs.infosup)) {
      $('#erreurClub').fadeOut();
    } else {
      $('#erreurClub').fadeIn();
      erreur = true;
    }

    // Si notre variable "erreur" est à vrai :
    if(erreur) {
      // On stop l'envoi du formulaire en empéchant l'exécution de l'évenement normal d'un submit, c'est a dire l'envoie du formulaire.
      event.preventDefault();
    }
    // Si il n'y a pas d'erreur, rien n'arrète l'envoie du formulaire, il est donc envoyé normalement car valide.
  });
});
