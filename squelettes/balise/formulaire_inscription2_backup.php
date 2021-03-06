<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('base/abstract_sql');

function balise_FORMULAIRE_INSCRIPTION2 ($p) {
	return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION2', array());}

// args[0] peut valoir "redac" ou "forum" 
function balise_FORMULAIRE_INSCRIPTION2_stat($args, $filtres) {
	//initialiser mode d'inscription
	$mode = $args[0];
	
	//utilisation possible #FORMULAIRE_INSCRIPTION2{forum,une_option,#ID_ARTICLE}
	// on recupere ensuite #ENV{option} et #ENV{article}
	$option = ($args[1])? $args[1] : '' ;  // sert a passer un argument a la balise
	$article = ($args[2])? $args[2] : '' ; // passe un id_article a la balise

	if(!$mode)
		$mode = $GLOBALS['meta']['accepter_inscriptions'] == 'oui' ? 'redac' : 'forum'; 
	
	if(!test_mode_inscription2($mode))
		return '';
	else return array($mode,$option,$article);
}

function balise_FORMULAIRE_INSCRIPTION2_dyn($mode,$option,$article) {
	//var_dump(lire_config('inscription2'));
	if (!test_mode_inscription2($mode)) 
		return _T('pass_rien_a_faire_ici');
	//recuperer les infos inser�es par le visiteur
	$var_user = array();
	foreach(lire_config('inscription2') as $cle => $val) {
		if($val!='' and !ereg("^.+_(obligatoire|fiche|table).*$", $cle)){
			if($cle== 'zones')
				continue;
				
			if($cle== 'statut_int' OR $cle== 'statut_nouveau')
				continue;	
			
			elseif($val!='' and $cle == 'username')
				$var_user['login'] = _request($cle);
			
			elseif($val!='' and $cle == 'naissance')
				$var_user[$cle] = _request('annee').'-'._request('mois').'-'._request('jour');
			
			elseif($val!='' and $cle == 'creation')
				$var_user[$cle] = date('Y-m-d');
			
			elseif(ereg("^categorie.*$", $cle)){
				$aux = _request('categories');
				if($aux != '0')
					$var_user['categorie'] = $aux;
			}
			elseif(ereg("^newsletter.*$", $cle)){
				$var_user['newsletters'] = _request('newsletters');
				$var_user['`spip_listes_format`'] = "html"; // bizare le _request('format') passe pas
			}
				
			elseif(ereg("^statut_interne.*$", $cle))
				$var_user['statut_interne'] = lire_config('inscription2/statut_interne');
			
			elseif(ereg("^statut_abonnement.*$", $cle)){
				$var_user['statut_abonnement'] = lire_config('inscription2/statut_abonnement');
				$var_user['abonnement'] =  _request('abonnement');
			}
			elseif($cle=='accesrestreint') 
				$var_user['zones'] = lire_config('inscription2/zones');
				
			else
				$var_user[$cle] = _request($cle);
		}
	}
		
	$aux = true;
	$commentaire = true;
	if($var_user['domaines']){
		include(find_in_path("inc/domaines.php"));
		$var_user['sites'] = $domaine[$var_user['domaines']]['sites'] ;
		$var_user['zone'] = $domaine[$var_user['domaines']]['zones'] ;
		foreach($var_user['sites'] as $val)
			$aux = ($aux or ereg("^.*".$val."$", $var_user['email']));
		if(!$aux)
			$aux = empty($var_user['sites']);
		if(!$aux)
			$message = _T('inscription2:mail_non_domaine');
	}
 
	$var_user['option'] = $option;
	$var_user['article'] = $article;
	// etape de validation
	if($option!='membres' AND $option!='benevole' AND $option!='provisoire'){ 
		if($var_user['email'] and ($retour = _request('retour'))){	
			$commentaire = true;
			$var_user['commentaires'] = $commentaire;
			return array("formulaires/inscription2", $GLOBALS['delais'],$var_user);
		}elseif($var_user['email'] and !($validation = _request('validation'))){	
			$commentaire = true;
			$var_user['commentaires'] = $commentaire;
			return array("formulaires/commander", $GLOBALS['delais'],$var_user,$validation);
		}
	}
			
	// enregistrement du nouvel inscrit
	if($var_user['email'] and $aux){
		$commentaire = message_inscription2($var_user, $mode);
		if (is_array($commentaire)) {
			$var_user['id_auteur'] = $commentaire['id_auteur'];
			$var_user['id_auteur_elargi'] = $commentaire['id_auteur_elargi'];
				if(defined('_DIR_PLUGIN_ABONNEMENT')) {
					$var_user['hash_article'] = $commentaire['hash_article'] ;
				 }
			$commentaire = true;
			}
	}
	
	$var_user['message'] = $message;
	$var_user['commentaires'] = $commentaire;
	$var_user['mode'] = $mode;
	$var_user['self'] = str_replace('&amp;','&',(self()));

	if($var_user['email']){
		if($option!='membres' AND $option!='benevole' AND $option!='provisoire'){
			return array("formulaires/commander", $GLOBALS['delais'],$var_user);
			}
		else
			if ($option=='membres'){
			return array("formulaires/paiement", $GLOBALS['delais'],$var_user);
			}
	
		return array("formulaires/inscription2_validation", $GLOBALS['delais'],$var_user);

	}else{
	return array("formulaires/inscription2", $GLOBALS['delais'],$var_user);
	}		
}

function test_mode_inscription2($mode) {
	return (($mode == 'redac' AND $GLOBALS['meta']['accepter_inscriptions'] == 'oui')
		OR ($mode == 'forum' AND ($GLOBALS['meta']['accepter_visiteurs'] == 'oui'
			OR $GLOBALS['meta']['forums_publics'] == 'abo')));}

function message_inscription2($var_user, $mode) {
	$row = spip_query("SELECT nom, statut, id_auteur, login, email, alea_actuel FROM `spip_auteurs` WHERE email=" . _q($var_user['email']));
	$row = spip_fetch_array($row);

	if (!$row) 							// il n'existe pas, creer les identifiants  
		return inscription2_nouveau($var_user);
	
	if ($row['statut'] == '5poubelle')	// irrecuperable
		return _T('form_forum_access_refuse');
	
	
if ($row['statut'] == 'aconfirmer'){	// deja inscrit
		envoyer_inscription2($row['id_auteur']);//RENVOYER MAIL D'INSCRIPTION 
		return _T('inscription2:mail_renvoye');
	}

	// est-ce un abonne � spip-listes ?
	$spip_listes_abo = spip_query("SELECT b.id, b.spip_listes_format, b.nom_famille, b.adresse FROM `spip_auteurs` a , `spip_auteurs_elargis` b WHERE a.email=" . _q($var_user['email'])." AND a.id_auteur=b.id_auteur");
	$spip_listes_abo = spip_fetch_array($spip_listes_abo);
	
	$format_spip_listes = array("texte","html","non");
		
	if ( in_array($spip_listes_abo['spip_listes_format'],$format_spip_listes) 
		AND $spip_listes_abo['nom_famille'] == ''
		AND $spip_listes_abo['adresse'] == ''
		){	// inscrit spip-listes
		$infos_auteur = array("id_auteur" => $row['id_auteur'] , "id_auteur_elargi" => $spip_listes_abo['id']);
		//maj
		return inscription2_maj($var_user,$infos_auteur);
	}


	return _T('form_forum_email_deja_enregistre');
}

function inscription2_nouveau($declaration){
	$declaration = inscription2_test_login($declaration);

	$declaration['statut'] = 'aconfirmer';
	//insertion des donn�es ds la table spip_auteurs
	foreach($declaration as $cle => $val){
		if($cle == 'newsletters' or $cle == 'zones' or $cle =='sites' or $cle == 'zone' or $cle =='abonnement' or $cle=='option' or $cle=='article')
			continue;
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login')
			$auteurs[$cle] = $val;
		else
			$elargis[$cle]= $val;
	}
	//insertion des donn�es dans la table spip_auteurs
	$declaration['alea_actuel'] = rand(1,99999);
	$auteurs['alea_actuel']=$declaration['alea_actuel'];
	$n = spip_abstract_insert('spip_auteurs', ('(' .join(',',array_keys($auteurs)).')'), ("(" .join(", ",array_map('_q', $auteurs)) .")"));
	$declaration['id_auteur'] = $n;
	$elargis['id_auteur'] = $n;
	$date = date('Y-m-d');
	//insertion des donn�es dans la table spip_auteurs_elargis
	if(isset($declaration['newsletters'])){
		foreach($declaration['newsletters'] as $value){
			if($value != '0')
				spip_query("INSERT INTO `spip_auteurs_listes` 
				(`id_auteur`, `id_liste`, `statut`, `date_inscription`) 
				VALUES ('$n', '$value', 'valide','$date')");
	}}
	if(isset($declaration['zones']) && !$declaration['article']){
		foreach($declaration['zones'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`)VALUES ('$n', '$value')");
	}
	if(isset($declaration['domaines']) and $declaration['zone'] and lire_config('plugin/ACCESRESTREINT')){
		foreach($declaration['zone'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`)VALUES ('$n', '$value')");
	}
	
	$n = spip_abstract_insert('`spip_auteurs_elargis`', ('(' .join(',',array_keys($elargis)).')'), ("(" .join(", ",array_map('_q', $elargis)) .")"));
	$declaration['id_auteur_elargi'] = $n ;
	
	if($declaration['abonnement']){
		$value = $declaration['abonnement'] ;	
			spip_query("INSERT INTO `spip_auteurs_elargis_abonnements` (`id_auteur_elargi`, `id_abonnement`) VALUES ('$n', '$value')");
	}
	
	if(isset($declaration['article']) AND $declaration['article'] > 0){
		$value = $declaration['article'] ;	
		include_spip('inc/acces');
		$montant =  lire_config('abonnement/prix_article');
		$hash = creer_uniqid();
			spip_query("INSERT INTO `spip_auteurs_elargis_articles` (`id_auteur_elargi`, `id_article`, `statut_paiement` , `hash`,`montant`) VALUES ('$n', '$value', 'a_confirmer','$hash','$montant')");
		$declaration['hash_article'] = $hash ;

	}

	return $declaration;
}


function inscription2_maj($declaration,$infos_auteur){
		
	$declaration['statut'] = 'aconfirmer';
	$alea_actuel = rand(1,99999);

	//update nom et statut des donn�es ds la table spip_auteurs
	if(intval($infos_auteur['id_auteur']) > 0)
		spip_query("UPDATE spip_auteurs SET nom="._q($declaration['nom']).", statut="._q($declaration['statut'])." , pass='' , alea_actuel=$alea_actuel WHERE id_auteur=".$infos_auteur['id_auteur']) ;
	
	
	foreach($declaration as $cle => $val){
		if($cle == 'newsletters' or $cle == '`spip_listes_format`'  or $cle == 'zones' or $cle =='sites' or $cle == 'zone' or $cle =='abonnement' or $cle=='option' or $cle=='article')
			continue;
		if ($cle == 'email' or $cle == 'nom' or $cle == 'bio' or $cle == 'statut' or $cle == 'login')
			continue;
		else
			$elargis[$cle] = "$cle = "._q($val) ;
	}
	

	//insertion des donn�es dans la table spip_auteurs_elargis
	if(isset($declaration['newsletters'])){
		foreach($declaration['newsletters'] as $value){
			if($value != '0' AND intval($infos_auteur['id_auteur']) > 0){
				spip_query("DELETE FROM `spip_auteurs_listes` WHERE id_auteur=".$infos_auteur['id_auteur']." AND id_liste=$value");
				spip_query("INSERT INTO `spip_auteurs_listes` 
				(`id_auteur`, `id_liste`, `statut`, `date_inscription`) 
				VALUES ('".$infos_auteur['id_auteur']."', '$value', 'valide','$date')");
				}
	}}
	if(isset($declaration['zones']) && !$declaration['article']){
		foreach($declaration['zones'] as $value)
			spip_query("DELETE FROM `spip_zones_auteurs` WHERE id_auteur=".$infos_auteur['id_auteur']." AND id_zone=$value");
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`) VALUES ('".$infos_auteur['id_auteur']."', '$value')");
	}
	if(isset($declaration['domaines']) and $declaration['zone'] and lire_config('plugin/ACCESRESTREINT')){
		foreach($declaration['zone'] as $value)
			spip_query("INSERT INTO `spip_zones_auteurs` (`id_auteur`, `id_zone`)VALUES ('".$infos_auteur['id_auteur']."', '$value')");
	}
	// update
	
	if(intval($infos_auteur['id_auteur']) > 0)
		$q1 = spip_query("update `spip_auteurs_elargis` set ".join(', ', $elargis)." where `id_auteur`='".$infos_auteur['id_auteur']."'");
		
		
	$declaration['id_auteur_elargi'] = $infos_auteur['id_auteur_elargi'] ;
	
	
	if($declaration['abonnement']){
		$value = $declaration['abonnement'] ;	
			spip_query("INSERT INTO `spip_auteurs_elargis_abonnements` (`id_auteur_elargi`, `id_abonnement`) VALUES ('".$infos_auteur['id_auteur_elargi']."', '$value')");
	}
	
	if(isset($declaration['article']) AND $declaration['article'] > 0){
		$value = $declaration['article'] ;	
		include_spip('inc/acces');
		$montant =  lire_config('abonnement/prix_article');
		$hash = creer_uniqid();
			spip_query("INSERT INTO `spip_auteurs_elargis_articles` (`id_auteur_elargi`, `id_article`, `statut_paiement` , `hash`,`montant`) VALUES ('".$infos_auteur['id_auteur_elargi']."', '$value', 'a_confirmer','$hash','$montant')");
		$declaration['hash_article'] = $hash ;

	}

	return $declaration;
}


function inscription2_test_login($var_user) {
	if(!isset($var_user['login']))
		$var_user['login']=$var_user['nom'];
	$login = $var_user['login'];
	for ($i = 1; ; $i++) {
		$n = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs WHERE login='$login' LIMIT 1"));
		if (!$n){
			$var_user['login'] = $login;
			return $var_user;
		}
		$login = $var_user['login'].$i;
	}
}
?>
