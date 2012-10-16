<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');


// Balise de traitement des donnéees du formulaire
function formulaires_inscription_concert_charger_dist($id_evenement) {

	include_spip('inc/session');

	//On récupèrere les champs

	$id_art= _request('id_art');	
	$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille=');
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
	
	if($id_auteur=session_get('id_auteur'))$auteur=sql_fetsel('*','spip_auteurs a LEFT JOIN spip_auteurs_elargis b ON a.id_auteur=b.id_auteur','a.id_auteur='.$id_auteur);

	
	//$auteur['pays']=sql_getfetsel('nom','spip_geo_pays','id_pays='.$auteur['pays']);


	if(_request('opt_out'))	$opt_out=1;
	else $opt_out=0;
	
	$retour['editable'] = true;
	

	$bouton=_request('bouton');	
	
	$valeurs=array (
				'id_art'=> $id_art,				
				'id_evenement'=> $id_evenement,
				'nom_famille'	=> $nom_famille,
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
				'confirmer'=>_request('confirmer'),
				'soumettre'=>_request('soumettre'),										
				);
	if(is_array($auteur))$valeurs=array_merge($valeurs,$auteur);	
		
	//On retourne au formulaire d'inscription
	return $valeurs;
}
	
function formulaires_inscription_concert_verifier_dist($id_evenement){

	
	//On récupèrere les champs
	$id_evenement= intval(_request('id_evenement'));
	$id_art= _request('id_art');	
	$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille');
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
	
	
	//initialise le tableau des erreurs
	$erreurs = array();	
	
	//On contrôle les données du formulaire			

		
		//email invalide
		foreach(array('nom_famille','prenom','email','adresse','code_postal','ville','pays') as $champ) {
				if (!_request($champ)) {
					$erreurs[$champ] = _T('spip:info_obligatoire');
					}
			}
		if ($email AND !email_valide($email)){
			$erreurs['email']=_T('asso:adherent_message_email_invalide');
		}
		
		$inscrits=$membres+$non_membres+$prix_ext_1_valeur+$prix_ext_2_valeur+$prix_ext_3_valeur+$prevente_non_membre;
			
		if($inscrits<1)$erreurs['inscrits']=_T('asso:activite_message_inscrits_manquant');
		
		

		/*if (count($erreurs)>0) $erreurs['bouton']='soumettre';
		elseif(!_request('confirmer'))	$erreurs['bouton']='confirmer';	 // si pas d'erreur
		*/						
	
	return $erreurs;	
}

function formulaires_inscription_concert_traiter_dist($id_evenement){

	$retour = array();
		
	//On récupèrere les champs
	$id_evenement= intval(_request('id_evenement'));
	$id_art= _request('id_art');	
	$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille');
	$prenom=_request('prenom');
	$numero=_request('numero');
	$boite=_request('boite');			
	$code_postal=_request('code_postal');		
	$ville=_request('ville');			
	$pays=_request('pays');			
	$lang=_request('lang');	
	$membres=_request('membres');		
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
	$commentaire=_request('commentaire');
	$voir=_request('voir');
	$prix_membres=_request('prix_membres');
	$prix_non_membres=_request('prix_non_membres');
	$prix_prevente_non_membre=_request('prix_prevente_non_membre');
	$prix_ext_1=_request('prix_ext_1');
	$prix_ext_2=_request('prix_ext_2');
	$prix_ext_3=_request('montant_ext_3');
		
	$montant_membres=$membres*$prix_membres;
	$montant_non_membres=$non_membres*$prix_non_membres;
	if($prix_prevente_non_membre)$montant_prevente_non_membre=$non_membres*$prix_prevente_non_membre;
	$montant_ext_1=$prix_ext_1_valeur*$prix_ext_1;
	$montant_ext_1=$prix_ext_2_valeur*$prix_ext_2;	
	$montant_ext_1=$prix_ext_3_valeur*$prix_ext3;	

	$inscrits=$membres+$non_membres+$prevente_non_membre+$prix_ext_1_valeur+$prix_ext_2_valeur+$prix_ext_3_valeur;
	
	//Les totaux
	$montant =  $montant_membres+$montant_non_membres+$montant_ext_1+$montant_ext_2+$montant_ext_3;
	if($montant_prevente_non_membre)$montant_prevente=$montant_membres+$montant_prevente_non_membre;


	//$type=_request('type');	
	$compte=$GLOBALS['association_metas']['compte']	;
	
	if(_request('opt_out'))	$opt_out=1;
	else $opt_out=0;

	$pays_activite=sql_getfetsel('nom','spip_geo_pays','id_pays='.$pays);
	
	//enregistrement dans la table
	$valeurs=array(
		'id_evenement' => $id_evenement,
		'lang' => $lang,			
		'id_adherent' => $id_adherent,
		'nom' => $nom_famille,
		'prenom' => $prenom,	
		'numero' => $numero,
		'boite' => $boite,	
		'code_postal' => $code_postal,								
		'ville' => $ville,	
		'pays' => extraire_multi($pays_activite),											
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
			
		spip_log($n,$n ? "activite_enregistre" : "activite_non_enregistre");
		
		// Inscription à la newsletter si pas décoché
		if(!$opt_out){
			//teste si il existe un auteur avec cet email
			if($id_registre=sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($email))){
				// Si pas encore abonne on l'abonne à la liste de la langue courante
				if(!$abonne=sql_getfetsel('id_auteur','spip_auteurs_listes','id_auteur='.$id_registre)){
					$id_liste=sql_getfetsel('id_liste','spip_listes','id_liste IN (1,7,8) AND lang='.sql_quote($lang));
					$valeurs_auteurs_listes=array(
						'id_auteur'=>$id_registre,
						'id_liste'=>$id_liste,
						'date_inscription'=>date('Y-m-d G:i:s'),	
						'statut'=>'valide',
						'format'=>'html'											
						);
					sql_insertq('spip_auteurs_listes',$valeurs_auteurs_listes);
					sql_update('spip_auteurs_elargis',array('spip_listes_format'=>'html'),'id_auteur='.$id_registre);
				}
			}
			// Sinon on lui cré un compte et on l'inscrit à la liste de langue courante
			else{
				include_spip('inc/spiplistes_api');
				$pass = creer_pass_aleatoire ();
				$htpass = generer_htpass($pass);
				$alea_actuel = creer_uniqid();
				$alea_futur = creer_uniqid();
				$pass = md5($alea_actuel.$pass);
				$login= spiplistes_login_from_email ($email) ;

				$id_liste=sql_getfetsel('id_liste','spip_listes','id_liste IN (1,7,8) AND lang='.sql_quote($lang));
				
				// création du compte
				$valeurs_auteur=array(
					'lang' => $lang,			
					'nom' => $prenom.' '.$nom_famille,
					'email' => $email,
					'pass'=>$pass,
					'login'=>$login,								
					'htpass'=>$htpass,
					'alea_actuel'=>$alea_actuel,
					'alea_futur'=>$alea_futur,
					'statut'=>'6forum',						
					'maj'=>date('Y-m-d G:i:s')																
					);	
					
				$id_auteur=sql_insertq('spip_auteurs',$valeurs_auteur);	
									
				$valeurs_auteur_elargis=array(
					'id_auteur' =>$id_auteur,
					'prenom' => $prenom,
					'nom_famille' => $nom_famille,			
					'adresse' => $adresse,
					'code_postal' => $code_postal,
					'ville' => $ville,	
					'numero' => $numero,
					'code_postal' => $code_postal,								
					'pays' => $pays,	
					'telephone' => $telephone,																
					'creation' => date('Y-m-d G:i:s'),
					'spip_listes_format' => 'html'					
					);
					
				sql_insertq('spip_auteurs_elargis',$valeurs_auteur_elargis);	
					
				// abonnement à la liste de contexte
				
				$valeurs_auteurs_listes=array(
					'id_auteur'=>$id_auteur,
					'id_liste'=>$id_liste,
					'date_inscription'=>date('Y-m-d G:i:s'),	
					'statut'=>'valide',
					'format'=>'html'											
					);
						
				sql_insertq('spip_auteurs_listes',$valeurs_auteurs_listes);	
								
								
			
			}
			
			
		
		}

		// envoi des emails
			
		$data = sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement " );
		$activite=$data['titre'];
		$date=$data['date_debut'];
		$lieu=$data['lieu'];
		
		$email_asso=lire_config('email_webmaster');
		$sujet=_T('asso:activite_message_sujet',array('nomasso'=>$nom_asso));
		
		$valeurs['montant_prevente']=$montant_prevente;
		
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
		

					
		$message= _T('activite_reservation_message_confirmation_inscription',
				array(
				'activite'=>$activite,
				'date'=>affdate($date,'d-m-Y'),
				'lieu'=>$lieu,
				'nom'=>$prenom.' '.$nom_famille,
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
			$envoyer_mail($email, $sujet, $message);	
			
			spip_log($email.' '.$message,'email_reservation_cours');
			
			
			$valeurs['html'] ='ok';
			$inscriptions=recuperer_fond('inc/participants',$valeurs);
			
			$retour['message_ok']=_T('activite_reservation_message_confirmation_inscription_html',array(
					'id_adherent'=>$id_adherent,
					'id_asso'=>$id_adherent,	
					'activite'=>$activite,
					'date'=>affdate($date,'d-m-Y'),
					'lieu'=>$lieu,
					'nom'=>$prenom.' '.$nom_famille,
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
			

			
			return $retour;
}

?>
