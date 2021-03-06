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

if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_ajout_cotisation(){
		
	include_spip('inc/autoriser');
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
			$commencer_page = charger_fonction('commencer_page', 'inc');
			echo $commencer_page(_T('asso:ajout_de_cotisation')) ;
			association_onglets();
			echo debut_gauche("",true);
			ajout_cotisation(intval(_request('id')));
			echo fin_gauche(), fin_page();
	}
}
	
function ajout_cotisation($id_auteur)
{
$url_retour=_request('url_retour');	
	$url_action_cotisations = generer_url_ecrire('action_cotisations');
			
	$data = sql_fetsel("*",_ASSOCIATION_AUTEURS_ELARGIS, "id_auteur=$id_auteur");
	
	if ($data) {

		$nom_famille=$data['nom_famille'];
		$prenom=$data['prenom'];
		$categorie=$data['categorie'];
		$validite=$data['validite'];
	
		list($annee, $mois, $jour) = explode("-",$validite); 

		$categorie = sql_fetsel("duree, cotisation", "spip_asso_categories", "id_categorie=" . intval($categorie));

		if ($jour==0 OR $mois==0 OR $annee==0)
		  list($annee, $mois, $jour) = explode("-",date('Y-m-d'));
		$mois+=$categorie['duree'];
		$validite=date("Y-m-d", mktime(0, 0, 0, '12', '31', date('Y')));

		$justification = _L('Cotisation') . ' ' . $nom . ' ' . $prenom;

		echo debut_boite_info(true);
		echo '<strong>'.$nom_famille.' '.$prenom.'</strong><br />';
		echo $categorie ? ('<strong>'.$categorie.'</strong>') :'';
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo debut_droite("",true);

		$res = '<label for="date"><strong>'._T('asso:date_du_paiement_AAAA-MM-JJ').' :</strong></label>';
		$res .= '<input name="date" type="text" value="'.date('Y-m-d').'" id="date" class="formo" />';
		$res .= '<label for="montant"><strong>'._T('asso:montant_paye_en_euros').' :</strong></label>';
		$res .= '<input name="montant" type="text" value="'.$categorie['cotisation'].'" id="montant" class="formo" />';
		$res .= '<label for="journal"><strong>'._T('asso:prets_libelle_mode_paiement').'&nbsp;:</strong></label>';
		$res .= '<select name="journal" type="text" id="journal" class="formo" />';
		$sql = sql_select('*', 'spip_asso_plan', "classe=". _q($GLOBALS['association_metas']['classe_banques']), '', "code") ;
		while ($banque = sql_fetch($sql)) {
			$res .= '<option value="'.$banque['code'].'"> '.$banque['intitule'].' </option>';
		}
		$res .= '<option value="don"> Don </option>';
		$res .= '</select>';
		$res .= '<label for="validite"><strong>'._T('asso:Validite').' :</strong></label>';
		$res .= '<input name="validite" type="text" value="'.$validite.'" id="validite" class="formo" />';
		$res .= '<label for="justification"><strong>'._T('asso:Justification').' :</strong></label>';
		$res .= '<input name="justification" type="text" value="'. htmlspecialchars($justification) . '" id="justification" class="formo" />';
		
		$res .= '<div style="float:right;"><input name="submit" type="submit" value="';
		if ( isset($action)) {$res .= _T('asso:bouton_'.$action);}
		else {$res .= _T('asso:bouton_envoyer');}
		$res .= '" class="fondo" /></div>';
		

		echo debut_cadre_relief(  "", false, "", _T('asso:nouvelle_cotisation'));
		echo redirige_action_post('cotisation', $id_auteur, 'voir_adherent', "id=$id_auteur&url_retour=$url_retour", $res);

		echo fin_cadre_relief(true);  
	}
}
?>
