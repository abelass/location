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

function locations_instituer($id_location, $c, $calcul_details=true){

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
        spip_log("editer_location $id_location refus " . join(' ', $c),'locations');

    }

    //Vérifier si les dates sont disponibles
  
    if($statut=='valide'){
       
        $horaires=lire_config('location/afficher_horaires')?'oui':'';  
        $verifier_reservations=charger_fonction('verifier_reservations','inc');
        $erreur_reservation=$verifier_reservations($row['objet'],$row['id_objet'],$row['date_debut'],$row['date_fin'],$id_location,$horaires);
        
     //echo serialize($erreur_reservation);
    if(count($erreur_reservation)>0){
       
       // return _L("Impossible d'instituer $objet : non connu en base");
        $champs['statut']=$statut_ancien;
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
    objet_editer_heritage('locations',$id_location, $id_rubrique, $statut_ancien, $champs, $calcul_rub);
    

    // Invalider les caches
    include_spip('inc/invalideur');
    suivre_invalideur("id='id_location/$id_location");
    

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