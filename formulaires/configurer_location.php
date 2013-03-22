
<?php



// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_location_saisies_dist(){

    $liste_objets=lister_tables_objets_sql();


 
    //les objets de spip
     $objets=array();
     foreach($liste_objets as $cle=>$valeur){
       $valeur['editable'];
         if(isset($valeur['principale']) AND $valeur['editable']=='oui'){
            $objets[$valeur['type']]=_T($valeur['texte_objets']);
            }
     
     //Le statuts du plugin
     foreach($liste_objets['spip_locations']['statut_textes_instituer'] AS $statut=>$label){
         $statuts[$statut]=_T($label);
     }
     
     
 }
	include_spip('inc/config');
	include_spip('inc/plugin');
	//include_spip('location_fonctions');
	$config = lire_config('location');

	$choix_expediteurs = array(
			'webmaster' => _T('location:notifications_expediteur_choix_webmaster'),
			'administrateur' => _T('location:notifications_expediteur_choix_administrateur'),
			'email' => _T('location:notifications_expediteur_choix_email')
	);
	
	if (defined('_DIR_PLUGIN_FACTEUR')){
		$choix_expediteurs['facteur'] = _T('location:notifications_expediteur_choix_facteur');
	}

	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_parametres',
				'label' => _T('location:parametres_cfg_titre')
			),

            'saisies' => array(
                array(
                    'saisie' => 'checkbox',
                    'options' => array(
                        'nom' => 'objets_location',
                        'label' => _T('location:label_objets_location'),
                        'datas'=>$objets,
                        'defaut'=> $config['objets_location']
                    )
                ),
               array(
                    'saisie' => 'oui_non',
                    'options' => array(
                        'nom' => 'afficher_horaires',
                        'label' => _T('location:label_afficher_horaires'),
                        'defaut'=> $config['afficher_horaires']
                    )
                ),
               array(
                    'saisie' => 'selection',
                    'options' => array(
                        'nom' => 'statut_defaut',
                        'datas' => $statuts,
                        'defaut'=> 'valide',
                        'cacher_option_intro' => 'on',
                        'label' => _T('location:label_statut_defaut'),
                        'defaut'=> $config['statut_defaut']
                    )
                ), 
               array(
                    'saisie' => 'selection',
                    'options' => array(
                        'nom' => 'statut_defaut_public',
                        'label' => _T('location:label_statut_defaut_public'),
                         'datas' => $statuts,
                         'defaut'=> $config['statut_defaut_public']?$config['statut_defaut_public']:($config['statut_defaut']?$config['statut_defaut']:'attente'),
                    )
                )                               
            )            
		),
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications',
				'label' => _T('location:notifications_cfg_titre')
			),
			'saisies' => array(
				array(
					'saisie' => 'explication',
					'options' => array(
						'nom' => 'exp1',
						'texte' => _T('location:notifications_explication')
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'activer',
						'label' => _T('location:notifications_activer_label'),
						'explication' => _T('location:notifications_activer_explication'),
						'defaut' => $config['activer']
					)
				)
			)
		),	
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_notifications_parametres',
				'label' => _T('location:notifications_parametres'),
				'afficher_si' => '@activer@ == "on"',
			),
			'saisies' => array(
				array(
					'saisie' => 'selection_multiple',
					'options' => array(
						'nom' => 'quand',
						'label' => _T('location:notifications_quand_label'),
						'explication' => _T('location:notifications_quand_explication'),
						'cacher_option_intro' => 'on',
						'datas' => $statuts,
						'defaut' => $config['quand']
					)
				),
				
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'expediteur',
						'label' => _T('location:notifications_expediteur_label'),
						'explication' => _T('location:notifications_expediteur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['expediteur'],
						'datas' => $choix_expediteurs
					)
				),
				
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_webmaster',
						'label' => _T('location:notifications_expediteur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'defaut' => $config['expediteur_webmaster'],
						'afficher_si' => '@expediteur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'expediteur_administrateur',
						'label' => _T('location:notifications_expediteur_administrateur_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'defaut' => $config['expediteur_administrateur'],
						'afficher_si' => '@expediteur@ == "administrateur"',
					)
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'expediteur_email',
						'label' => _T('location:notifications_expediteur_email_label'),
						'defaut' => $config['expediteur_email'],
						'afficher_si' => '@expediteur@ == "email"',
					)
				),
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'vendeur',
						'label' => _T('location:notifications_vendeur_label'),
						'explication' => _T('location:notifications_vendeur_explication'),
						'cacher_option_intro' => 'on',
						'defaut' => $config['vendeur'],
						'datas' => array(
							'webmaster' => _T('location:notifications_vendeur_choix_webmaster'),
							'administrateur' => _T('location:notifications_vendeur_choix_administrateur'),
							'email' => _T('location:notifications_vendeur_choix_email')
						)
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_webmaster',
						'label' => _T('location:notifications_vendeur_webmaster_label'),
						'statut' => '0minirezo',
						'cacher_option_intro' => "on",
						'webmestre' => 'oui',
						'multiple' => 'oui',
						'defaut' => $config['vendeur_webmaster'],
						'afficher_si' => '@vendeur@ == "webmaster"',
					)
				),
				array(
					'saisie' => 'auteurs',
					'options' => array(
						'nom' => 'vendeur_administrateur',
						'label' => _T('location:notifications_vendeur_administrateur_label'),
						'statut' => '0minirezo',
						'multiple' => 'oui',
						'cacher_option_intro' => "on",
						'defaut' => $config['vendeur_administrateur'],
						'afficher_si' => '@vendeur@ == "administrateur"',
					)
				),
				
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'vendeur_email',
						'label' => _T('location:notifications_vendeur_email_label'),
						'explication' => _T('location:notifications_vendeur_email_explication'),
						'defaut' => $config['vendeur_email'],
						'afficher_si' => '@vendeur@ == "email"',
					)
				),
				array(
					'saisie' => 'oui_non',
					'options' => array(
						'nom' => 'client',
						'label' => _T('location:notifications_client_label'),
						'explication' => _T('location:notifications_client_explication'),
						'defaut' => $config['client'],
					)
				)
			)
		)
	);
}

?>