<?php
/**
 * Gestion du formulaire de d'édition de location
 *
 * @plugin     Location
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Location\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_location
 *     Identifiant du location. 'new' pour un nouveau location.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_location_identifier_dist($id_location='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_location), $associer_objet));
}

/**
 * Chargement du formulaire d'édition de location
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_location
 *     Identifiant du location. 'new' pour un nouveau location.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_location_charger_dist($id_location='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('location',$id_location,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de location
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_location
 *     Identifiant du location. 'new' pour un nouveau location.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_location_verifier_dist($id_location='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){

	$erreurs = formulaires_editer_objet_verifier('location',$id_location, array('date_debut', 'date_fin'));
    
    // verifier et changer en datetime sql la date envoyee
    $verifier = charger_fonction('verifier', 'inc');
    $champs = array('date_debut','date_fin');
    $normaliser = null;
    foreach($champs AS $champ){
       if ($erreur = $verifier(_request($champ), 'date', array('normaliser'=>'datetime'), $normaliser)) {
        $erreurs[$champ] = $erreur;
    // si une valeur de normalisation a ete transmis, la prendre.
    } elseif (!is_null($normaliser)) {
        set_request($champ, $normaliser);
    }
    }

    return $erreurs;
}

/**
 * Traitement du formulaire d'édition de location
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_location
 *     Identifiant du location. 'new' pour un nouveau location.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param string $associer_objet
 *     Éventuel `objet|x` indiquant de lier le location créé à cet objet,
 *     tel que `article|3`
 * @param int $lier_trad
 *     Identifiant éventuel d'un location source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du location, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_location_traiter_dist($id_location='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$res = formulaires_editer_objet_traiter('location',$id_location,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
 
	// Un lien a prendre en compte ?
	if ($associer_objet AND $id_location = $res['id_location']) {
		list($objet, $id_objet) = explode('|', $associer_objet);

		if ($objet AND $id_objet AND autoriser('modifier', $objet, $id_objet)) {
			include_spip('action/editer_liens');
			objet_associer(array('location' => $id_location), array($objet => $id_objet));
			if (isset($res['redirect'])) {
				$res['redirect'] = parametre_url ($res['redirect'], "id_lien_ajoute", $id_location, '&');
			}
		}
	}
	return $res;

}


?>