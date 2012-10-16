<?php

/**
 * Action d'export CSV des auteurs
 * 
 * Plugin Inscription 2 Import / Export
 * 
 */
function action_gestion_festival_dist(){

	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	

	
	list($action,$id_activite)= explode("|",$arg);
	
	switch($action){		
		case 'traiter':
			sql_updateq('spip_asso_activites',array('statut'=>'traite'),'id_activite='.$id_activite);
		
			break;
		case 'eliminer':		
			sql_delete('spip_asso_activites','id_activite='.$id_activite);
			sql_delete('spip_asso_activites','id_activite_source='.$id_activite);		
			break;
	
		}
}
?>
