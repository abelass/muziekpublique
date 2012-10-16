<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');


	function exec_edit_activite(){
		$prix_membres = _request('prix_membres');
		$prix_non_membres = _request('prix_non_membres');
		$type = _request('type');
		$date_paiement=date("Y-m-d");
		if ($type=='cours'){
			$montant=$prix_membres;
			};
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip('inc/acces_page');
		
		$url_action_activites = generer_url_ecrire('action_activites');
		$url_retour = $_SERVER["HTTP_REFERER"];
		
		$action=$_REQUEST['action'];
		if($action=='ajoute'){$id_evenement=$_REQUEST['id'];}
		else {$id_activite=$_REQUEST['id'];}	
		
		$query = spip_query ("SELECT * FROM spip_asso_activites WHERE id_activite='$id_activite' ");
		while ($data = spip_fetch_array($query)){
			$id_evenement=$data['id_evenement'];
			$nom=$data['nom'];
			$id_adherent=$data['id_adherent'];
			$membres=$data['membres'];
			$non_membres=$data['non_membres'];
			$inscrits=$data['inscrits'];
			$email=$data['email'];
			$telephone=$data['telephone'];
			$adresse=$data['adresse'];
			$montant=$data['montant'];
			$date=$data['date'];
			$statut=$data['statut'];
			$commentaire=$data['commentaires'];
			$date_paiement=$data['date_paiement'];
			$source_paiement=$data['$source_paiement'];
			$validite=$data['$validite'];

		}
		
		$query = spip_query ("SELECT * FROM spip_evenements WHERE id_evenement='$id_evenement' ");
		while ($data = spip_fetch_array($query)){
			$titre=$data['titre'];
			$date_debut=$data['date_debut'];
			$lieu=$data['lieu'];
			$id_article=$data['id_article'];
			$prix_membres=$data['prix_membres'];
			$prix_non_membres=$data['prix_non_membres'];
			$prix_prevente_non_membres=$data['prix_prevente_non_membres'];

			$query = spip_query ("SELECT * FROM spip_evenements WHERE id_evenement_source='$id_evenement' ORDER BY date_debut DESC LIMIT 1 ");
			while ($data = spip_fetch_array($query)){
			$date_validite=$data['date_debut'];
			if ($id_rubrique=='27' or $id_rubrique=='1' or $id_rubrique=='40' ){$type='cours';};
			if ($id_rubrique=='8' or $id_rubrique=='26' or $id_rubrique=='39' ){$type='concert';};
			}

			$query = spip_query ("SELECT * FROM spip_articles WHERE id_article='$id_article' ");
			while ($data = spip_fetch_array($query)){
			$id_rubrique=$data['id_rubrique'];
			if ($id_rubrique=='27' or $id_rubrique=='1' or $id_rubrique=='40' ){$type='cours';};
			if ($id_rubrique=='8' or $id_rubrique=='26' or $id_rubrique=='39' ){$type='concert';};
			}
		}
		if ($type=='cours'){$montant=$prix_prevente_non_membres;};
		debut_page(_T('asso:activite_titre_mise_a_jour_inscriptions'));
		
		association_onglets();
		
		debut_gauche();
		
		debut_boite_info();
		echo '<div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">Activit&eacute; n&deg;<br />';
		echo '<span class="spip_xx-large">';
		{echo $id_evenement;}
		echo '</span></div>';
		echo '<br /><div style="font-weight: bold; text-align: center" class="verdana1 spip_xx-small">'.$titre.'</div>';
		echo '<br /><div>'.association_date_du_jour().'</div>';		
		fin_boite_info();	
		
		debut_raccourcis();
		icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
		fin_raccourcis();
		
		debut_droite();
		debut_cadre_relief(  "", false, "", $titre = _T('asso:activite_titre_mise_a_jour_inscriptions'));

		echo '<form method="post" action="'.$url_action_activites.'">';
		echo '<label for="date"><strong>'._T('asso:activite_libelle_date').' (AAAA-MM-JJ) :</strong></label>';
		echo '<input name="date" type="text" value="'.$date.'" id="date" class="formo" />';
		echo '<label for="nom"><strong>'._T('asso:activite_libelle_nomcomplet').' :</strong></label>';
		echo '<input name="nom"  type="text" value="'.$nom.'" id="nom" class="formo" />';
		echo '<label for="id_membre"><strong>'._T('asso:activite_libelle_adherent').' :</strong></label>';
		echo '<input name="id_membre" type="text" value="'.$id_adherent.'" id="id_membre" class="formo" />';
		if ($type!='cours'){
			echo '<label for="membres"><strong>'._T('asso:activite_libelle_membres').' ('._T('prix').':'.$prix_membres.'&euro;) :</strong></label>';
			echo '<input name="membres"  type="text" value="'.$membres.'" id="membres" class="formo" />';
			echo '<label for="non_membres"><strong>'._T('asso:activite_libelle_non_membres').' ('._T('prix').':'.$prix_non_membres.'&euro;) :</strong></label>';
			echo '<input name="non_membres"  type="text" size="40" value="'.$non_membres.'" id="non_membrese" class="formo" />';
			echo '<label for="inscrits"><strong>'._T('asso:activite_libelle_nombre_inscrit').' :</strong></label>';
			echo '<input name="inscrits"  type="text" value="'.$inscrits.'" id="inscrits" class="formo" />';
			}
		else	{
			echo '<input name="inscrits"  type="hidden" value="1" id="inscrits"  />';
			};

		echo '<label for="email"><strong>'._T('asso:activite_libelle_email').' :</strong></label>';
		echo '<input name="email"  type="text" value="'.$email.'" id="email" class="formo" />';
		echo '<label for="telephone"><strong>'._T('asso:activite_libelle_telephone').' :</strong></label>';
		echo '<input name="telephone" type="text" value="'.$telephone.'" id="telephone" class="formo" />';
		echo '<label for="adresse"><strong>'._T('asso:activite_libelle_adresse_complete').' :</strong></label>';
		echo '<textarea name="adresse" id="adresse" class="formo" />'.$adresse.'</textarea>';
		echo '<label for="montant"><strong>'._T('asso:activite_libelle_montant_inscription').' :</strong></label>';
		if ($type='cours'){
		echo '<div>'._T('prix_annee').' :'.$prix_prevente_non_membres.'</div>';
		echo '<div>'._T('prix_periode1').' :'.$prix_membres.'</div>';
		echo '<div>'._T('prix_periode2').' :'.$prix_non_membres.'</div>';
		};
		echo '<input name="montant"  type="text" value="'.$montant.'" id="montant" class="formo" />';
		echo '<label for="statut"><strong>'._T('asso:activite_libelle_statut').' ok :</strong></label>';
		echo '<input name="statut"  type="checkbox" value="ok"';
		if ($statut=='ok') { echo ' checked="checked"'; }
		echo ' id="statut" /><br />';
		echo '<label for="date_paiement"><strong>'._T('asso:activite_libelle_date_paiement').' :</strong></label>';
		echo '<input name="date_paiement" type="text" value="'.$date_paiement.'"id="date_paiement" class="formo" />';
		echo '<label for="source_paiement"><strong>'._T('source_paiement').' :</strong></label>';
		echo '<select name="source_paiement" type="text" id="source_paiement" class="formo" />';
		if($source_paiement){
		echo '<option value="transference_bancaire">'.$source_paiement.'<option>';
					};
		echo '<option value="transference_bancaire"> Transference bancaire </option>';
		echo '<option value="cash"> Cash </option>';
		echo '<option value="paypal"> Paypal </option>';
		echo '</select>';
		echo '<label for="validite"><strong>'._T('validite_inscription').' :</strong></label>';
		echo '<input name="validite" type="text" value="'.$date_validite.'"id="validitet" class="formo" />';
		echo '<label for="commentaire"><strong>'._T('asso:activite_libelle_commentaires').' :</strong></label>';
		echo '<textarea name="commentaire" id="commentaire" class="formo" />'.$commentaire.'</textarea>';
		echo '<input name="action" type="hidden" value="'.$action.'">';
		echo '<input name="id_evenement" type="hidden" value="'.$id_evenement.'">';
		echo '<input name="id_activite" type="hidden" value="'.$id_activite.'">';
		echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
		
		echo '<div style="float:right;">';
		echo '<input name="submit" type="submit" value="';
		if ( isset($action)) {echo _T('asso:bouton_'.$action);}
		else {echo _T('asso:bouton_envoyer');}

		echo '" class="fondo" /></div>';
		echo '</form>';
		
		fin_cadre_relief();  
		fin_page();
	}  
?>