<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_location_client_destinataires_dist($id_location, $options) {	
	$id_auteur=sql_getfetsel("id_auteur","spip_auteurs_liens","id_objet=".$id_location." AND objet='location'");
	return array($id_auteur);	
}

?>