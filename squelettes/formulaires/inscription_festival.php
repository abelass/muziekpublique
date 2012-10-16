<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function array_flatten($array) { 
   if (!is_array($array)) { 
     return FALSE; 
   } 
   $result = array(); 
   foreach ($array as $key => $value) { 
     if (is_array($value)) { 
       $result = array_merge($result, array_flatten($value)); 
     } 
     else { 
       $result[$key] = $value; 
     } 
   } 
   return $result; 
 }


function donnees_evenements($id_article){
	
	$id_evenement=array();
	$places_evenement=array();
	$evenement_full=array();	
	$places_disponibles=array();	
	$horaires=array();
	$date_debut=array();
	
	$evenements=sql_select('id_evenement,places,date_debut','spip_evenements','id_article='.$id_article,'','titre');
	//Les données des évenements
	while($data=sql_fetch($evenements)){
		$sql='';
		$id_evenement[]=$data['id_evenement'];
		$places_evenement[$data['id_evenement']]=$data['places'];
		$date_debut[$data['id_evenement']]=$data['date_debut'];
		
		$sql=sql_select('titre','spip_mots,spip_mots_evenements','spip_mots.id_mot=spip_mots_evenements.id_mot AND id_evenement='.$data['id_evenement'].' AND id_groupe=27');
		while($h=sql_fetch($sql)){
			$horaires[$data['id_evenement']][]=$h['titre'];
			}
		}
			$evenement_full=array();
	// On calcule si un concert est plein	
	foreach($horaires AS $evenement=>$heures){
		foreach($heures AS $heure){
			$reserve=sql_countsel('spip_asso_activites','id_evenement='.$evenement.' AND heure='.sql_quote($heure));
			if($reserve>= $places_evenement[$evenement]){
				$evenement_full[$evenement][]=$heure;	
				}
			$places_disponibles	[$evenement][$heure]=$places_evenement[$evenement]-$reserve;
			}
		
		}

	$return=array('id_evenement'=>$id_evenement,'evenement_full'=>$evenement_full,'places_disponibles'=>$places_disponibles);
	
	//echo serialize($return);
	return $return;
	}




// Balise de traitement des donnéees du formulaire
function formulaires_inscription_festival_charger_dist($id_article) {

	include_spip('inc/session');

	//On récupèrere les champs
	

	$article=sql_fetsel('prix_membres,prix_non_membres,inscription_active','spip_articles','id_article='.$id_article);
	if($id_auteur=session_get('id_auteur'))$auteur=sql_fetsel('*','spip_auteurs a LEFT JOIN spip_auteurs_elargis b ON a.id_auteur=b.id_auteur','a.id_auteur='.$id_auteur);
	//$auteur['pays']=sql_getfetsel('nom','spip_geo_pays','id_pays='.$auteur['pays']);
	
	//divers données en relation avec l'évenement
	
	$evenements=donnees_evenements($id_article);

	$evenements_festival=$evenements['id_evenement'];
	$places_disponibles=$evenements['places_disponibles'];	
	
	//echo serialize($id_evenement);
	$evenement_full=$evenements['evenement_full'];

	//$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille');
	$prenom=_request('prenom');
	$numero=_request('numero');
	$boite=_request('boite');			
	$code_postal=_request('code_postal');		
	$ville=_request('ville');			
	$pays=_request('pays');			
	$membres=_request('membres');
	$lang=_request('lang');					
	$email=_request('email');
	$adresse=_request('adresse');
	$telephone=_request('telephone');
	$montant=_request('montant');
	$commentaire=_request('commentaire');
	$voir=_request('voir');
	$id_article=(_request('id_article')?_request('id_article'):$id_article);
	
	




	if(_request('opt_out'))	$opt_out=1;
	else $opt_out=0;
	
	$retour['editable'] = true;
	

	$bouton=_request('bouton');	
	
	$valeurs=array (
				'evenements'=>$evenements_festival,
				'evenement_full'=>$evenement_full,				
				'id_article'=> $id_article,				
				'id_evenement'=> $id_evenement,
				'nom_famille'	=> $nom_famille,
				'id_adherent'=> $id_adherent,
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
				'membres' => _request('membres'),
				'non_membres' => _request('non_membres'),
				'commentaire'=> $commentaire,
				'opt_out'=>_request('opt_out'),	
				'evenement_full'=>$evenement_full,
				'inscription_active'=>$article['inscription_active'],
				'places_disponibles'=>$places_disponibles,
																		
				);
	
	$reservations=array();
	foreach($evenements_festival AS $evenement){
		$valeurs['concert_'.$evenement]='';	
		}			
				
	if(is_array($auteur))$valeurs=array_merge($valeurs,$auteur);
	$valeurs=array_merge($valeurs,$article);	

	$valeurs[_hidden].='<input type="hidden" name="evnmnts" value="'.implode(',',$evenements_festival).'"/>';	
	$valeurs[_hidden].='<input type="hidden" name="id_article" value="'.$id_article.'"/>';	
	
	/*$valeurs[_hidden].='<input type="hidden" name="prix_membres" value="'.$article['prix_membres'].'"/>';	
	$valeurs[_hidden].='<input type="hidden" name="prix_non_membres" value="'.$article['prix_non_membres'].'"/>';	*/
	//On retourne au formulaire d'inscription
	return $valeurs;
}
	
function formulaires_inscription_festival_verifier_dist($id_article){

	
	//On récupèrere les champs
	$id_evenement= explode(',',_request('evnmnts'));
	$id_art= _request('id_art');	
	$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille');
	$prenom=_request('prenom');
	$numero=_request('numero');
	$boite=_request('boite');			
	$code_postal=_request('code_postal');		
	$ville=_request('ville');			
	$pays=_request('pays');			
	$prix=_request('prix');
	$lang=_request('lang');						
	$email=_request('email');
	$adresse=_request('adresse');
	$telephone=_request('telephone');
	$montant=_request('montant');
	$membres=_request('membres');
	$non_membres=_request('non_membres');
	$commentaire=_request('commentaire');
	$nombre_res_oblig=3;
	
	$inscrits=$membres+$non_membres;
	
		$erreurs = array();
	
	
	if($inscrits==0)$erreurs['inscrits']=_T('asso:activite_message_inscrits_manquant');
		


	// On récupère les réservations
	$reservations=array();
	foreach($id_evenement AS $evenement){
		if(_request('concert_'.$evenement)){
			$reservations[$evenement]=_request('concert_'.$evenement);		
			}
		}
		
	//	echo serialize($reservations);	
	// On réduit l'array à une dimension
	$reservations_flat=array_flatten($reservations);	
		
	//initialise le tableau des erreurs

	$count=count($reservations_flat);
	
	// Contrôle si le numero de concert min/max est atteint
	if($count!=$nombre_res_oblig){
		$erreurs['reservation_2']=_T('nombre_concerts_obligatoire',array('nombre'=>$nombre_ses_oblig));
		}
		
	// On détecte les réservation faite à la même heure
	$array_unique=array_unique($reservations_flat);	
	if(count($array_unique)!=$count){
		$test=array();
		$doublons=array();
		foreach($reservations_flat AS $h){
			if(in_array($h,$test))$doublons[]=$h;
			else $test[]=$h;
			}
		$doublons=implode(',',array_unique($doublons));
		
		$erreurs['reservation_2']=_T('erreur_horaires_doublons',array('doublons'=>$doublons));
		}
		
			
	//On détecte si il ya encore des places diponibles
	$evenements=donnees_evenements($id_article);
	$evenement_full=$evenements['evenement_full'];
	$places_disponibles=$evenements['places_disponibles'];
	
	//echo serialize($places_disponibles);
	//echo serialize($reservations);

	foreach($reservations AS $evenement=>$heure){

if($inscrits>$places_disponibles[$evenement][$heure])$erreurs['reservation'][$evenement][$heure]=_T('erreur_sold_out').' '._T('place').' : '.$places_disponibles[$evenement][$heure];
				

		} 

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
		

		/*if (count($erreurs)>0) $erreurs['bouton']='soumettre';
		elseif(!_request('confirmer'))	$erreurs['bouton']='confirmer';	 // si pas d'erreur
		*/						
	
	return $erreurs;	
}

function formulaires_inscription_festival_traiter_dist($id_article){

	$retour = array();
		
	//On récupèrere les champs
	$id_evenement= explode(',',_request('evnmnts'));
	$id_adherent=_request('id_adherent');
	$nom_famille=_request('nom_famille');
	$prenom=_request('prenom');
	$numero=_request('numero');
	$boite=_request('boite');			
	$code_postal=_request('code_postal');		
	$ville=_request('ville');			
	$pays=_request('pays');			
	$lang=_request('lang');	
	$prix=_request('prix');							
	$email=_request('email');
	$adresse=_request('adresse');
	$telephone=_request('telephone');
	$commentaire=_request('commentaire');
	$membres=_request('membres');
	$non_membres=_request('non_membres');	
	
	$inscrits=$membres+$non_membres;
	
	$prix=sql_fetsel('prix_membres,prix_non_membres','spip_articles','id_article='.$id_article);
	
	$prix_membres= $prix['prix_membres'];
	$prix_non_membres= $prix['prix_non_membres'];
	
	$montant=($membres*$prix_membres)+($non_membres*$prix_non_membres);
	
	// On récupère les réservations
	$reservations=array();
	foreach($id_evenement AS $evenement){
		if(_request('concert_'.$evenement)){
			$reservations[$evenement]=_request('concert_'.$evenement);		
			}
		}


	//$type=_request('type');	
	$compte=$GLOBALS['association_metas']['compte']	;
	
	if(_request('opt_out'))	$opt_out=1;
	else $opt_out=0;

	$pays_activite=sql_getfetsel('nom','spip_geo_pays','id_pays='.$pays);
	
	//enregistrement dans la table
	$valeurs_principal=array(
		'lang' => $lang,			
		'id_article' => $id_article,
		'id_adherent' => $id_adherent,
		'nom' => $nom_famille,
		'prenom' => $prenom,	
		'numero' => $numero,
		'boite' => $boite,	
		'code_postal' => $code_postal,								
		'ville' => $ville,	
		'pays' => extraire_multi($pays_activite),											
		'email' => $email,
		'adresse' => $adresse,
		'telephone' => $telephone,
		'montant' => $montant,
		'commentaire' => $commentaire,
		'opt_out' => $opt_out,			
		'date' => date('Y-m-d G:i:s'),
		'membres' => $membres,
		'non_membres' => $non_membres
		
		);

	
		// On enregistre les données principales
		$id_activite = sql_insertq('spip_asso_activites',$valeurs_principal); 
			
		spip_log($id_activite,$id_activite ? "activite_enregistre" : "activite_non_enregistre");
		
		// les détails des différents concerts
		if (is_array($reservations)){
			foreach($reservations AS $id_evenement=>$horaires){
				$valeurs=array();
				$date_evenement='';
				$date_evenement=sql_getfetsel('date_debut','spip_evenements','id_evenement='.$id_evenement);
				if(is_array($horaires)){
					foreach($horaires AS $heure){
						$tab_couples = array();
						$i = 1;
						while ($i <= $inscrits) {
						    $i++; 
						    $tab_couples[]=array(
										'id_activite_source'=>$id_activite,
										'id_article' => $id_article,
										'id_evenement'=>$id_evenement,
										'heure'=>$heure,
										);
							sql_insertq_multi('spip_asso_activites', $tab_couples);		
								}
								
						
						//sql_insertq('spip_asso_activites',$valeurs); 						
						}

						
					}
					else{
						$tab_couples = array();
						$i = 1;
						while ($i <= $inscrits) {
						    $i++; 
						    $tab_couples[]=array(
										'id_activite_source'=>$id_activite,
										'id_article' => $id_article,
										'id_evenement'=>$id_evenement,
										'heure'=>$horaires,
										);	
								}
						sql_insertq_multi('spip_asso_activites', $tab_couples);		
					}			
				}
			}
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
			
		//$data = sql_fetsel("*", "spip_evenements", "id_evenement=$id_evenement " );
		$activite=$data['titre'];
		$date=$data['date_debut'];
		$lieu=$data['lieu'];
		
		$email_asso=lire_config('email_webmaster');
		$sujet=_T('activite_message_sujet');
		
		$valeurs=array(
			'id_article'=>$id_article,
			'id_auteur'=>$id_auteur,
			'reservations'=>$reservations,
			'montant_membres'=>$membres*$prix_membres,
			'montant_non_membres'=>$non_membres*$prix_non_membres,
			'membres'=>$membres,
			'non_membres'=>$non_membres,
			);
		
		$message=recuperer_fond('inc/donnees_reservation_festival',$valeurs);


			
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail($email, $sujet, $message);	
			
			spip_log($email.' '.$message,'email_reservation_festival');
			
			$message=recuperer_fond('inc/donnees_reservation_festival_form',$valeurs);
			
			$retour['message_ok']=	$message;
			

			
			return $retour;
}

?>
