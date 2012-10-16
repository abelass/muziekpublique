<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/charsets');
include_spip('inc/lang');
include_spip('inc/headers');
include_spip('public/assembler');
include_spip('balise/formulaire_adherent');

	// Balise independante du contexte ici
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE ($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION_ACTIVITE', array());
}

	//function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_stat($args, $filtres) {
		// Si le moteur n'est pas active, pas de balise
		//if ($GLOBALS['meta']["activer_moteur"] != "oui")
			//return '';
		
		// filtres[0] doit etre un script (a revoir)
		//else
		  //return array($filtres[0], $args[0]);
	//}
	 
// Balise de traitement des donnéees du formulaire
function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_dyn() {
		
		//On récupèrere les champs
	$id_evenement= intval(_request('id_evenement'));
	$id_art= _request('id_art');	
	$id_adherent=_request('id_adherent');
	$nom=_request('nom');
	$prenom=_request('prenom');
	$numero=_request('numero');
	$boite=_request('boite');			
	$code_postal=_request('code_postal');		
	$ville=_request('ville');			
	$pays=_request('pays');			
	$membres=_request('membres');
	$lang=_request('lang');	
	$non_membres=_request('non_membres');
	$prevente_non_membre=_request('prevente_non_membre');	
	$prix_ext_1_valeur=_request('prix_ext_1_valeur');
	$prix_ext_2_valeur=_request('prix_ext_2_valeur');
	$prix_ext_3_valeur=_request('prix_ext_3_valeur');
	$prix_ext_1_titre=_request('prix_ext_1_titre');
	$prix_ext_2_titre=_request('prix_ext_2_titre');
	$prix_ext_3_titre=_request('prix_ext_3_titre');						
	$email=_request('email');
	$adresse=_request('adresse');
	$telephone=_request('telephone');
	$montant=_request('montant');
	$commentaire=_request('commentaire');
	$voir=_request('voir');
	
	
	//$type=_request('type');	
	$compte=$GLOBALS['association_metas']['compte']	;
	
	if(_request('opt_out'))	$opt_out=1;
	else $opt_out=0;
	
	$inscrits=$membres+$non_membres+$prix_ext_1_valeur+$prix_ext_2_valeur+$prix_ext_3_valeur+$prevente_non_membre;
	
	$bouton=_request('bouton');
		
	if (_request('confirmer')){		
			
		//enregistrement dans la table
		$valeurs=array(
			'id_evenement' => $id_evenement,
			'lang' => $lang,			
			'id_adherent' => $id_adherent,
			'nom' => $nom,
			'prenom' => $prenom,	
			'numero' => $numero,
			'boite' => $boite,	
			'code_postal' => $code_postal,								
			'ville' => $ville,	
			'pays' => $pays,											
			'membres' => $membres,
			'non_membres' => $non_membres,
			'prix_ext_1_valeur' => $prix_ext_1_valeur,
			'prix_ext_1_titre' => $prix_ext_1_titre,
			'prix_ext_2_valeur' => $prix_ext_2_valeur,
			'prix_ext_2_titre' => $prix_ext_2_titre,
			'prix_ext_3_valeur' => $prix_ext_3_valeur,
			'prix_ext_3_titre' => $prix_ext_3_titre,
			'prix_ext_3_titre' => $prix_ext_3_titre,
			'prevente_non_membre'=>$prevente_non_membre,											
			'inscrits' => $inscrits,
			'email' => $email,
			'adresse' => $adresse,
			'telephone' => $telephone,
			'montant' => $montant,
			'commentaire' => $commentaire,
			'opt_out' => $opt_out,			
			'date' => date('Y-m-d'));
		
		$n = sql_insertq('spip_asso_activites',$valeurs); 
			
		spip_log($n ? "enregistre activite $n" : "actvite non inseree");

		// envoi des emails
			
		$data = sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement " );
		$activite=$data['titre'];
		$date=$data['date_debut'];
		$lieu=$data['lieu'];
		
		$nom_asso='Muziekpublique';
		$email_asso=lire_config('email_webmaster');
		$expediteur=$nom_asso.' <'.$email_asso.'>';		
		$sujet=_T('asso:activite_message_sujet',array('nomasso'=>$nom_asso));
		
		$inscriptions=recuperer_fond('inc/participants',$valeurs);

		//au webmaster
		
		/*if($opt_out) $opt_out = 'Je ne veux pas recevoir de mail';
		$message = _T('asso:activite_message_webmaster',array(
				'activite'=>$activite,
				'date'=>association_datefr($date),
				'lieu'=>$lieu,
				'nom'=>$nom,
				'id_adherent'=>$id_adherent,
				'id_asso'=>$id_adherent,				
				'inscriptions'=>$inscriptions,
				'email'=>$email,
				'telephone'=>$telephone,
				'commentaire'=>$commentaire,
				'nomasso'=>$nom_asso,											
				'inscrits' => $inscrits,
				'email' => $email,
				'adresse' => $adresse,
				'montant' => $montant,
				'commentaire' => $commentaire,	
				'compte'=>$compte,	
				'opt_out'=>$op_out,				
			));
			
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
		
		$envoyer_mail($email_asso, $sujet, $message, $expediteur);*/
			
		//au demandeur
		if($prix_ext_1_valeur)$prix_ext_1=$prix_ext_1_titre.' : '.$prix_ext_1_valeur;
		if($prix_ext_2_valeur)$prix_ext_2=$prix_ext_2_titre.' : '.$prix_ext_2_valeur;
		if($prix_ext_3_valeur)$prix_ext_3=$prix_ext_3_titre.' : '.$prix_ext_3_valeur;
		

					
		$message= _T('asso:activite_reservation_message_confirmation_inscription',
				array(
				'activite'=>$activite,
				'date'=>affdate($date,'d-m-Y'),
				'lieu'=>$lieu,
				'nom'=>$nom,
				'id_adherent'=>$id_adherent,
				'id_asso'=>$id_adherent,				
				'inscriptions'=>$inscriptions,
				'email'=>$email,
				'telephone'=>$telephone,
				'commentaire'=>$commentaire,
				'nomasso'=>$nom_asso,											
				'inscrits' => $inscrits,
				'email' => $email,
				'adresse' => $adresse,
				'montant' => $montant,
				'commentaire' => $commentaire,	
				'compte'=>$compte,							
				));
			
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail($email, $sujet, $message, $expediteur);	
			
			
			$valeurs['html'] ='ok';
			$inscriptions=recuperer_fond('inc/participants',$valeurs);
			
			return _T('asso:activite_reservation_message_confirmation_inscription_html',array(
				'id_adherent'=>$id_adherent,
				'id_asso'=>$id_adherent,	
				'activite'=>$activite,
				'date'=>affdate($date,'d-m-Y'),
				'lieu'=>$lieu,
				'nom'=>$nom,
				'id_adherent'=>$id_adherent,
				'inscriptions'=>$inscriptions,
				'email'=>$email,
				'telephone'=>$telephone,
				'commentaire'=>$commentaire,
				'nomasso'=>$nom_asso,											
				'inscrits' => $inscrits,
				'email' => $email,
				'adresse' => $adresse,
				'montant' => $montant,
				'commentaire' => $commentaire,	
				'compte'=>$compte,				
			));
			
		}
		else {
			if (_request('soumettre')){
				
				//On contrï¿½le les donnï¿½es du formulaire			
				$bouton='confirmer';	 // si pas d'erreur
				
				//email invalide
				$erreur=array();
				foreach(array('nom','prenom','email','adresse','code_postal','ville','pays') as $champ) {
						if (!_request($champ)) {
							$erreur[$champ] = _T('spip:info_obligatoire');
							}
					}
				if ($email AND !email_valide($email)){
					$erreur['email']=_T('asso:adherent_message_email_invalide');
				}
				
				if (count($erreur)>0) $bouton='soumettre';
											
				//on retourne les infos ï¿½ un formulaire de previsualisation		
				return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_inscription_activite_previsu',0,
						array(
							'id_evenement'=> $id_evenement,
							'id_art'=> $id_art,							
							'nom'		=> $nom,
							'prenom' => $prenom,	
							'numero' => $numero,
							'boite' => $boite,	
							'code_postal' => $code_postal,								
							'ville' => $ville,	
							'pays' => $pays,									
							'id_adherent'=> $id_adherent,
							'membres'=> $membres,
							'non_membres'=> $non_membres,
							'inscrits'	=> $inscrits,
							'email'		=> $email,
							'telephone'	=> $telephone,
							'adresse'=> $adresse,
							'montant' => $montant,
							'commentaire'=> $commentaire,
							'bouton'=> $bouton,
							'erreur' => $erreur,							
							'voir'=> $voir,
							'type'=> $type,							
							'prix_ext_1_valeur'=>$prix_ext_1_valeur,
							'prix_ext_2_valeur'=>$prix_ext_2_valeur,
							'prix_ext_3_valeur'=>$prix_ext_3_valeur,
							'prevente_non_membre'=>$prevente_non_membre,
							'opt_out'=>_request('opt_out')													
						)
					),
					false
				);
			}
		}		
		
		//On retourne au formulaire d'inscription
		return array (
			'formulaires/formulaire_inscription_activite',0, 
			array (
				'id_art'=> $id_art,				
				'id_evenement'=> $id_evenement,
				'nom'		=> $nom,
				'id_adherent'=> $id_adherent,
				'membres'=> $membres,
				'non_membres'=> $non_membres,
				'prenom' => $prenom,	
				'numero' => $numero,
				'boite' => $boite,	
				'code_postal' => $code_postal,								
				'ville' => $ville,	
				'pays' => $pays,						
				'inscrits'	=> $inscrits,
				'email'		=> $email,
				'telephone'	=> $telephone,
				'adresse'=> $adresse,
				'montant' => $montant,
				'commentaire'=> $commentaire,
				'voir'=> $voir,
				'type'=> $type,				
				'prix_ext_1_valeur'=>$prix_ext_1_valeur,
				'prix_ext_2_valeur'=>$prix_ext_2_valeur,
				'prix_ext_3_valeur'=>$prix_ext_3_valeur,	
				'opt_out'=>_request('opt_out'),				
				)
			);
		
	}

?>
