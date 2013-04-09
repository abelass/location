<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_location_vendeur_destinataires_dist($id_location, $options) {	
	include_spip('inc/config');
	$config = lire_config('location');
	return $config['vendeur_'.$config['vendeur']];
}

?>