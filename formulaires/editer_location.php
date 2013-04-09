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
   @param string $limite_objets
 *     Where pour la requete des éléments â afficher

 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_location_charger_dist($id_location='new', $retour='', $associer_objet='', $lier_trad=0, $config_fonc='', $row=array(), $hidden='',$limite_objets=''){
	$valeurs = formulaires_editer_objet_charger('location',$id_location,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
    include_spip('inc/config');
    $valeurs['afficher_horaires']=lire_config('location/afficher_horaires')?'oui':'';  
    
    $statut=lire_config('location/statut_defaut','valide');
    
    //Traitement différents pour l'espace public
    $statut=$valeurs['statut'];
    if(!$statut AND _request('exec')){
        $valeurs['prive']=_request('exec');
        $statut=lire_config('location/statut_defaut_public',$statut);
    }
    $id_objet=_request('id_objet')?_request('id_objet'):$valeurs['id_objet'];

    //Les objets choisis  via config 
    $objets=lire_config('location/objets_location',array());
    if(count($objets)>1){
        foreach($objets AS $o){
            $valeurs['objets_dispo'][$o]=_T(objet_info($o,'texte_objets'));
            }
         $objet=_request('objet')?_request('objet'):$valeurs['objet'];
        }
    else $objet=$objets[0];
    include_spip('inc/pipelines_ecrire');
    $e = trouver_objet_exec($objet);
    $id_table_objet=$e['id_table_objet'];
    $table = table_objet_sql($objet);

    //Les objets effectifs correspondant à l'objet sélectionné
    if($id_table_objet AND $table)$sql=sql_select($id_table_objet,$table,$limite_objets);
    while($data=sql_fetch($sql)){
        $valeurs['id_objets_dispo'][$data[$id_table_objet]]=generer_info_entite($data[$id_table_objet],$objet,'titre');
    }
    
    $valeurs['date']=date('Y-m-d G:i:s');

    
    $valeurs['_hidden'].='<input type="hidden" name="id_objet" value="'.$id_objet.'"/>';
    if($objet)$valeurs['_hidden'].='<input type="hidden" name="objet" value="'.$objet.'"/>'; 
    $valeurs['_hidden'].='<input type="hidden" name="afficher_horaires" value="'.$valeurs['afficher_horaires'].'"/>'; 

    $valeurs['_hidden'].='<input type="hidden" name="statut" value="'.$statut.'"/>'; 

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
    include_spip('inc/texte');
    $erreurs=array();
    

    $champs = array('date_debut','date_fin');
    $normaliser = null;
    $horaires=_request('afficher_horaires');
    $date_debut=_request('date_debut');
    $date_fin=_request('date_fin');
    

    // verifier et changer en datetime sql la date envoyee
    $verifier = charger_fonction('verifier', 'inc');
    
    $options=array('normaliser'=>'datetime');
    foreach($champs AS $champ){
        $options['format']='jma';
        $r=_request($champ);
        
        // quelques navigateur en html5 retournent la date différement
        if($horaires AND preg_match('#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#',$r['date'])){
            list($annee,$mois,$jour) = explode('-',$r['date']);
           $r['date']=$jour.'/'.$mois.'/'.$annee; 
        }
        elseif(!$horaires AND preg_match('#^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$#',$r)){
            list($annee,$mois,$jour) = explode('-',$r);
            $r=$jour.'/'.$mois.'/'.$annee;
            }
       if ($erreur = $verifier($r, 'date',$options, $normaliser)) {
        $erreurs[$champ] = $erreur;
    // si une valeur de normalisation a ete transmis, la prendre.
    } elseif (!is_null($normaliser)) {
        set_request($champ, $normaliser);
      
    }
    }
    
    //On merge avec les controls faites par spip
	$erreurs=array_merge($erreurs,formulaires_editer_objet_verifier('location',$id_location, array('date_debut', 'date_fin','objet','id_objet')));
    
    //Date fin doit être postérieur à la date début
    if(_request('date_debut')>=_request('date_fin'))$erreurs['date_fin'] = _T('location:erreur_date_fin_inferieur');
    
    // Teste si l'id-objet correspond à un élément - utile seulement si pas le formulaire select
    if($objet=_request('objet') AND $id_objet=_request('id_objet')){

        $data_objet=lister_tables_objets_sql($table);
        
        
        $data_objet=lister_tables_objets_sql($table);
        //en testant on récupère le tire de l'élément s'i existe
        if(!$titre=generer_info_entite($id_objet,$objet,'titre'))$erreurs['id_objet']=_T('location:erreur_id_objet_objet_inexistant',array('id_objet'=>$id_objet,'objet'=>$objet));
        
        set_request('titre',$titre);
        
        //Vérifier si les dates sont disponibles
        $verifier_reservations=charger_fonction('verifier_reservations','inc');
        $erreur_reservation=$verifier_reservations($objet,$id_objet,_request('date_debut'),_request('date_fin'),$id_location,$horaires);
        if($erreur_reservation)$erreurs=array_merge($erreurs,$erreur_reservation);
        
        
        }
    
    // teste antispam pour l'espace public
    if(!_request('exec')){

        // si nospam est present on traite les spams
        if (include_spip('inc/nospam')) {
            $commentaire=_request('commentaire');
            // on analyse le texte
            $infos_texte = analyser_spams( $commentaire);
            if ($infos_texte['nombre_liens'] > 0) {
                // si un lien a un titre de moins de 3 caracteres = spam !
                if ($infos_texte['caracteres_texte_lien_min'] < 3) {
                    $erreurs['commentaire'] = _T('nospam:erreur_spam');
                }
                // si le texte contient plus de trois lien = spam !
                if ($infos_texte['nombre_liens'] >= 3)
                    $erreurs['commentaire'] = _T('nospam:erreur_spam');
            }
        }
        
    }


    //$erreurs['titre']='ok';

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