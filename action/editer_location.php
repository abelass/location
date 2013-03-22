<?php

/**
 * Instituer la location
 *
 * @plugin     Location
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Location\Action\
 */


if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Appelle toutes les fonctions de modification d'un objet
 * $err est un message d'erreur eventuelle
 *
 * @param string $objet
 * @param int $id
 * @param array|null $set
 * @return mixed|string
 */
function location_modifier($id_location, $set=null) {

    $table_sql = table_objet_sql('locations');
    $trouver_table = charger_fonction('trouver_table','base');
    $desc = $trouver_table($table_sql);
    if (!$desc OR !isset($desc['field'])) {
        spip_log("Objet $objet inconnu dans objet_modifier",_LOG_ERREUR);
        return _L("Erreur objet $objet inconnu");
    }
    include_spip('inc/modifier');

    $champ_date = '';
    if (isset($desc['date']) AND $desc['date'])
        $champ_date = $desc['date'];
    elseif (isset($desc['field']['date']))
        $champ_date = 'date';

    $white = array_keys($desc['field']);
    // on ne traite pas la cle primaire par defaut, notamment car
    // sur une creation, id_x vaut 'oui', et serait enregistre en id_x=0 dans la base
    $white = array_diff($white, array($desc['key']['PRIMARY KEY']));
    

    if (isset($desc['champs_editables']) AND is_array($desc['champs_editables'])) {
        $white = $desc['champs_editables'];
    }
    $c = collecter_requests(
        // white list
        $white,
        // black list
        array($champ_date,'statut','id_parent','id_secteur'),
        // donnees eventuellement fournies
        $set
    );

    // Si l'objet est publie, invalider les caches et demander sa reindexation
    if (objet_test_si_publie('locations',$id_location)){
        $invalideur = "id='locations/$id_location'";
        $indexation = true;
    }
    else {
        $invalideur = "";
        $indexation = false;
    }

    if ($err = objet_modifier_champs('locations', $id_location,
        array(
            'nonvide' => '',
            'invalideur' => $invalideur,
            'indexation' => $indexation,
             // champ a mettre a date('Y-m-d H:i:s') s'il y a modif
            'date_modif' => (isset($desc['field']['date_modif'])?'date_modif':'')
        ),
        $c))
        return $err;

    // Modification de statut, changement de rubrique ?
    // FIXME: Ici lorsqu'un $set est passé, la fonction collecter_requests() retourne tout
    //         le tableau $set hors black liste, mais du coup on a possiblement des champs en trop. 
    $c = collecter_requests(array($champ_date, 'statut', 'id_parent'),array(),$set);
    $err = location_instituer($id_location, $c);
    
    

    return $err;
}


function location_instituer($id_location, $c, $calcul_details=true){

    include_spip('inc/autoriser');
    include_spip('inc/modifier');
    
    $row = sql_fetsel('statut,objet,id_objet,date_debut,date_fin','spip_locations','id_location='.intval($id_location));

    $statut_ancien = $statut = $row['statut'];

    $champs = array();
    

    $s = isset($c['statut']) ? $c['statut'] : $statut;
    
    // On ne modifie le statut que si c'est autorisé
    if ($s != $statut or ($d AND $d != $date)) {

        if (autoriser('modifier', 'locations', $id_commande))
            $statut = $champs['statut'] = $s;
        else
        spip_log("editer_location $id_location refus " . join(' ', $c),'location');

    }

    //Vérifier si les dates sont disponibles
  
    if($statut=='valide'){
       
        $horaires=lire_config('location/afficher_horaires')?'oui':'';  
        $verifier_reservations=charger_fonction('verifier_reservations','inc');
        $erreur_reservation=$verifier_reservations($row['objet'],$row['id_objet'],$row['date_debut'],$row['date_fin'],$id_location,$horaires);
        
     //echo serialize($erreur_reservation);
    if(count($erreur_reservation)>0){
       //echo 'erreur';
       set_request('statut',$statut_ancien);
       $champs['statut']=$statut_ancien;
       return _T("location:erreur_verifier_date");
        }
    }


    
    // Envoyer aux plugins
    $champs = pipeline(
        'pre_edition',
        array(
            'args' => array(
                'table' => 'spip_locations',
                'id_objet' => $id_location,
                'action' => 'instituer',
                'statut_ancien' => $statut_ancien,

            ),
            'data' => $champs
        )
    );

    if (!count($champs)) return;
    
    // Envoyer les modifications et calculer les héritages
    // Envoyer les modifs.
    objet_editer_heritage('location',$id_location, $id_rubrique, $statut_ancien, $champs, $calcul_rub);
    

    // Invalider les caches
    include_spip('inc/invalideur');
    suivre_invalideur("id='location/$id_location");
    

    // Pipeline
    pipeline(
        'post_edition',
        array(
            'args' => array(
                'table' => 'spip_locations',
                'id_objet' => $id_locations,
                'action' => 'instituer',
                'statut_ancien' => $statut_ancien,
            ),
            'data' => $champs
        )
    );
    
    // Notifications
    include_spip('inc/config');
    $config = lire_config('commandes');
    if (($statut != $statut_ancien) &&
         ($config['activer']) &&
         (in_array($statut,$config['quand'])) &&
         ($notifications = charger_fonction('notifications', 'inc', true))
        ) {

        // Determiner l'expediteur
        $options = array();
        if( $config['expediteur'] != "facteur" )
            $options['expediteur'] = $config['expediteur_'.$config['expediteur']];

        // Envoyer au vendeur et au client
        $notifications('commande_vendeur', $id_commande, $options);
        if($config['client'])
            $notifications('commande_client', $id_commande, $options);
    }

    return'';
}