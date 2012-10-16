<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


function formulaires_auteur_document_charger_dist($objet,$id_objet){
	
	$auteurs_dispo=array();
	
	$sql=sql_select('nom,id_auteur','spip_auteurs','photographe="oui"');
	
	while($data=sql_fetch($sql)){
		$auteurs_dispo[$data['nom']]=$data['id_auteur'];
	}
	$valeurs =array(
		'auteurs'=>'',
		'titre'=>'',		
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'afficher'=>_request('afficher'),
		'auteurs_dispo'=>$auteurs_dispo,
		);

	return $valeurs;
}



// http://doc.spip.org/@inc_editer_article_dist
function formulaires_auteur_document_traiter_dist($objet,$id_objet){
	$auteurs=_request('auteurs');
	$titre=_request('titre');
	if (is_array($auteurs)){
		foreach($auteurs AS $id_document=>$id_auteur){
			sql_updateq('spip_documents',array('auteur'=>$id_auteur,'titre'=>$titre[$id_document]),'id_document='.$id_document);
			}
		}

	return ;
}

?>
