<?php

/**
 *  Fichier généré par la Fabrique de plugin v5
 *   le 2013-03-19 09:39:43
 *
 *  Ce fichier de sauvegarde peut servir à recréer
 *  votre plugin avec le plugin «Fabrique» qui a servi à le créer.
 *
 *  Bien évidemment, les modifications apportées ultérieurement
 *  par vos soins dans le code de ce plugin généré
 *  NE SERONT PAS connues du plugin «Fabrique» et ne pourront pas
 *  être recréées par lui !
 *
 *  La «Fabrique» ne pourra que régénerer le code de base du plugin
 *  avec les informations dont il dispose.
 *
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

$data = array (
  'fabrique' => 
  array (
    'version' => 5,
  ),
  'paquet' => 
  array (
    'nom' => 'Location',
    'slogan' => 'Louez vos objets spip',
    'description' => 'Permet de louer vos objets spip',
    'prefixe' => 'location',
    'version' => '1.0.0',
    'auteur' => 'Rainer Müller',
    'auteur_lien' => 'http://mychacra.net/',
    'licence' => 'GNU/GPL',
    'categorie' => 'communication',
    'etat' => 'dev',
    'compatibilite' => '[3.0.6;3.0.*]',
    'documentation' => '',
    'administrations' => 'on',
    'schema' => '1.0.0',
    'formulaire_config' => 'on',
    'formulaire_config_titre' => 'Pramètres Locations',
    'fichiers' => 
    array (
      0 => 'autorisations',
      1 => 'fonctions',
      2 => 'pipelines',
    ),
    'inserer' => 
    array (
      'paquet' => '',
      'administrations' => 
      array (
        'maj' => '',
        'desinstallation' => '',
        'fin' => '',
      ),
      'base' => 
      array (
        'tables' => 
        array (
          'fin' => '',
        ),
      ),
    ),
    'scripts' => 
    array (
      'pre_copie' => '',
      'post_creation' => '',
    ),
    'exemples' => 'on',
  ),
  'objets' => 
  array (
    0 => 
    array (
      'nom' => 'Locations',
      'nom_singulier' => 'Location',
      'genre' => 'feminin',
      'logo_variantes' => '',
      'table' => 'spip_locations',
      'cle_primaire' => 'id_location',
      'cle_primaire_sql' => 'bigint(21) NOT NULL',
      'table_type' => 'location',
      'champs' => 
      array (
        0 => 
        array (
          'nom' => 'Date début',
          'champ' => 'date_debut',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'jj/mm/YYYY',
          'saisie_options' => '',
        ),
        1 => 
        array (
          'nom' => 'Date fin',
          'champ' => 'date_fin',
          'sql' => 'datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\'',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
            2 => 'obligatoire',
          ),
          'recherche' => '',
          'saisie' => 'date',
          'explication' => 'jj/mm/YYYY',
          'saisie_options' => '',
        ),
        2 => 
        array (
          'nom' => 'Type de location',
          'champ' => 'type_location',
          'sql' => 'varchar(50) NOT NULL DEFAULT \'\'',
          'caracteristiques' => 
          array (
            0 => 'versionne',
          ),
          'recherche' => '4',
          'saisie' => 'input',
          'explication' => '',
          'saisie_options' => '',
        ),
        3 => 
        array (
          'nom' => 'MAJ',
          'champ' => 'maj',
          'sql' => 'TIMESTAMP',
          'caracteristiques' => 
          array (
            0 => 'editable',
            1 => 'versionne',
          ),
          'recherche' => '',
          'saisie' => '',
          'explication' => '',
          'saisie_options' => '',
        ),
      ),
      'champ_titre' => '',
      'champ_date' => '',
      'statut' => 'on',
      'chaines' => 
      array (
        'titre_objets' => 'Locations',
        'titre_objet' => 'Location',
        'info_aucun_objet' => 'Aucune location',
        'info_1_objet' => 'Une location',
        'info_nb_objets' => '@nb@ locations',
        'icone_creer_objet' => 'Créer une location',
        'icone_modifier_objet' => 'Modifier cette location',
        'titre_logo_objet' => 'Logo de cette location',
        'titre_langue_objet' => 'Langue de cette location',
        'titre_objets_rubrique' => 'Locations de la rubrique',
        'info_objets_auteur' => 'Les locations de cet auteur',
        'retirer_lien_objet' => 'Retirer cette location',
        'retirer_tous_liens_objets' => 'Retirer toutes les locations',
        'ajouter_lien_objet' => 'Ajouter cette location',
        'texte_ajouter_objet' => 'Ajouter une location',
        'texte_creer_associer_objet' => 'Créer et associer une location',
        'texte_changer_statut_objet' => 'Cette location est :',
      ),
      'table_liens' => '',
      'roles' => '',
      'auteurs_liens' => 'on',
      'vue_auteurs_liens' => 'on',
      'echafaudages' => 
      array (
        0 => 'prive/squelettes/contenu/objets.html',
        1 => 'prive/objets/infos/objet.html',
        2 => 'prive/squelettes/contenu/objet.html',
      ),
      'autorisations' => 
      array (
        'objet_creer' => '',
        'objet_voir' => '',
        'objet_modifier' => '',
        'objet_supprimer' => '',
        'associerobjet' => '',
      ),
      'boutons' => 
      array (
        0 => 'menu_edition',
        1 => 'outils_rapides',
      ),
    ),
  ),
  'images' => 
  array (
    'paquet' => 
    array (
      'logo' => 
      array (
        0 => 
        array (
          'extension' => 'png',
          'contenu' => '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',
        ),
      ),
    ),
    'objets' => 
    array (
      0 => 
      array (
      ),
    ),
  ),
);

?>