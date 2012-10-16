<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	function exec_action_cotisations() {
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$action=$_POST['action'];
		$id_auteur= $_POST['id'];
		$nom_famille= $_POST['nom_famille'];
		$prenom= $_POST['prenom'];
		$date_paiement= $_POST['date_paiement'];
		$journal= $_POST['journal'];
		$montant= $_POST['montant'];
		$categorie= $_POST['categorie'];
		$justification =$_POST['justification'];
		$imputation=lire_config('association/pc_cotisations');
		$validite =$_POST['validite'];
		$url_retour=$_POST['url_retour'];
		$source_paiement=$_POST['source_paiement'];
		
		if($action=="ajoute") {
			spip_query( "INSERT INTO spip_asso_comptes (date, journal, recette, justification, imputation, id_journal) VALUES ("._q($date_paiement).", "._q($journal).", "._q($montant).", "._q($justification).", "._q($imputation).", "._q($id_auteur)." )" );
			spip_query( "UPDATE spip_auteurs_elargis SET statut_interne='ok', montant="._q($montant).", date_paiement="._q($date_paiement).", validite="._q($validite).", source_paiement="._q($source_paiement)." WHERE id_auteur="._q($id_auteur) );
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>