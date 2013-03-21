<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function notifications_commande_client_destinataires_dist($id_commande, $options) {	
	$id_auteur=sql_getfetsel("id_auteur","spip_commandes","id_commande=".$id_commande);
	return array($id_auteur);	
}

?>