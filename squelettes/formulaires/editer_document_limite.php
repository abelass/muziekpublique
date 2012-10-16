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

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/documents');

function formulaires_editer_document_limite_charger_dist($id_document, $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	include_spip('inc/session');

	$valeurs =array(
		'auteur'=>session_get('id_auteur'),
		'id_document'=>$id_document,
		);
	$valeurs['titre']=sql_getfetsel('titre','spip_documents','id_document='.$id_document);	

	return $valeurs;
}


function formulaires_editer_document_limite_verifier_dist($id_document, $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	$erreurs = formulaires_editer_objet_verifier('document',$id_document,is_numeric($id_document)?array():array('titre'));

	// verifier l'upload si on a demande a changer le document
	if (_request('joindre_upload') OR _request('joindre_ftp') OR _request('joindre_distant')){
		if (_request('copier_local')){
		}
		else {
			$verifier = charger_fonction('verifier','formulaires/joindre_document');
			$erreurs = array_merge($erreurs,$verifier($id_document));
		}
	}


	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_document_limite_traiter_dist($id_document, $id_parent='', $retour='', $lier_trad=0, $config_fonc='documents_edit_config', $row=array(), $hidden=''){
	$valeurs=array(
		'titre'=>_request('titre'),
		'auteur'=>_request('auteur'),
		);

	
	sql_updateq('spip_documents',$valeurs,'id_document='.$id_document);

	if (!isset($res['redirect']))
		$res['editable'] = true;
	if (!isset($res['message_erreur']))
		$res['message_ok'] = _L('Votre modification a &eacute;t&eacute; enregistr&eacute;e').$autoclose;

	return $res;
}

?>
