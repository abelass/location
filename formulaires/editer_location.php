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
    include_spip('inc/config');
    $objets=lire_config('location/objets_location',array());
    

    foreach($objets AS $o){
        $valeurs['objets_dispo'][$o]=_T(objet_info($o,'texte_objets'));
    }
    
    $objet=_request('objet')?_request('objet'):$valeurs['objet'];
    $e = trouver_objet_exec($objet);
    $id_table_objet=$e['id_table_objet'];
    
    
    $table = table_objet_sql($objet);
    
    if($id_table_objet AND $table)$sql=sql_select($id_table_objet,$table);
    
    while($data=sql_fetch($sql)){
        $valeurs['id_objets_dispo'][$data[$id_table_objet]]=generer_info_entite($data[$id_table_objet],$objet,'titre');
    }
    if(_request('exec'))$valeurs['prive']=_request('exec');
    if(_request('id_objet'))$valeurs['_hidden'].='<input type="hidden" name="id_objet" value="'._request('id_objet').'"/>';
    if(_request('objet'))$valeurs['_hidden'].='<input type="hidden" name="id_objet" value="'._request('objet').'"/>';    
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
    
        // verifier et changer en datetime sql la date envoyee
    $verifier = charger_fonction('verifier', 'inc');
    $champs = array('date_debut','date_fin');
    $normaliser = null;
    foreach($champs AS $champ){
        $r=_request($champ);

        $c=array($r['date'],$r['heure']);

       if ($erreur = $verifier($r, 'date', array('normaliser'=>'datetime'), $normaliser)) {
        $erreurs[$champ] = $erreur;
    // si une valeur de normalisation a ete transmis, la prendre.
    } elseif (!is_null($normaliser)) {
        set_request($champ, $normaliser);
      
    }
    }

	$erreurs = formulaires_editer_objet_verifier('location',$id_location, array('date_debut', 'date_fin','objet','id_objet'));
    
    if($objet=_request('objet')){
        // tester si l'objet est admis
        $table = table_objet_sql($objet);
        $tables_objets=lister_tables_objets_sql();
        $objets_exclus=array('message', 'petition', 'signature','syndic_article', 'depot', 'plugin', 'paquet', 'location');
        $objets=array();
        foreach($tables_objets As $o){
            if(isset($o['editable']) AND !in_array($o['type'],$objets_exclus))$objets[]=$o['type'];
        }
        $objets_dispo=implode(', ',$objets);
        
        if(!in_array($objet,$objets))$erreurs['objet'] = _T('location:erreur_objet',array('objets_dispo'=>$objets_dispo));
        // tester si l'objet est admis
          $data_objet=lister_tables_objets_sql($table);

        // Vérifier si l'objet existe
        if($id_objet=_request('id_objet')){
            include_spip('inc/texte');
            $data_objet=lister_tables_objets_sql($table);
            if(!$titre=generer_info_entite($id_objet,$objet,'titre'))$erreurs['id_objet']=_T('location:erreur_id_objet_objet_inexistant',array('id_objet'=>$id_objet,'objet'=>$objet));
            }
            set_request('titre',$titre);
        }
    

    if(_request('date_debut')>=_request('date_fin'))$erreurs['date_fin'] = _T('location:erreur_date_fin_inferieur');

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
    echo _request('titre').'ok';
    
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