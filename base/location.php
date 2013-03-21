<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Location
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Location\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function location_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['locations'] = 'locations';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function location_declarer_tables_objets_sql($tables) {

	$tables['spip_locations'] = array(
		'type' => 'location',
		'principale' => "oui",
		'field'=> array(
			"id_location"        => "bigint(21) NOT NULL",
			"titre"              => "text NOT NULL DEFAULT ''",
			"date_debut"         => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"date_fin"           => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
			"type_location"      => "varchar(50) NOT NULL DEFAULT ''",
			"commentaire"        => "text NOT NULL DEFAULT ''",
			"maj"                => "TIMESTAMP",
			"objet"              => "varchar(25) NOT NULL DEFAULT ''",
			"id_objet"           => "int(11) NOT NULL DEFAULT 0",
			"statut"             => "varchar(20)  DEFAULT '0' NOT NULL", 
			"maj"                => "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"        => "id_location",
			"KEY statut"         => "statut,id_objet,objet", 
		),
		'titre' => "'titre' AS titre, '' AS lang",
		 #'date' => "",
		'champs_editables'  => array('titre','commentaire','date_debut', 'date_fin', 'maj','objet','id_objet'),
		'champs_versionnes' => array('titre','commentaire','date_debut', 'date_fin', 'type_location', 'maj'),
		'rechercher_champs' => array("type_location" => 4, "objet" => 4, "id_objet" => 4,'titre'=>8,'commentaire'=>6),
		'tables_jointures'  => array(),
		'statut_textes_instituer' => array(
			'encours'     => 'location:texte_statut_encours',
			'attente'     => 'location:texte_statut_attente',			
			'valide'      => 'location:texte_statut_valide',
			'refuse'       => 'texte_statut_refuse',
			'poubelle'   => 'texte_statut_poubelle',
		),
		'statut'=> array(
			array(
				'champ'     => 'statut',
				'publie'    => 'valide',
				'previsu'   => 'valide,encours,attente',
				'post_date' => 'date', 
				'exception' => array('statut','tout')
			)
		),
		'statut_images' =>   array(
            'statut_location.png',
            'encours'=>'statut_encours.png',
            'attente'=>'statut_attente.png',            
            'valide'=>'statut_valide.png',
            'refuse'=>'statut_refuse.png',
            'poubelle'=>'statut_poubelle.png'),
		'texte_changer_statut' => 'location:texte_changer_statut_location', 
		

	);

	return $tables;
}



?>