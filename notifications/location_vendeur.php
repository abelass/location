<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_commande_vendeur_destinataires_dist($id_commande, $options) {	
	include_spip('inc/config');
	$config = lire_config('commandes');
	return $config['vendeur_'.$config['vendeur']];	

}

?>