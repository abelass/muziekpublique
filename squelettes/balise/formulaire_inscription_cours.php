<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');
include_spip('inc/charsets');
include_spip('inc/lang');
include_spip('inc/headers');
include_spip('public/assembler');
include_spip('balise/formulaire_adherent');


	// Balise independante du contexte ici
	function balise_FORMULAIRE_INSCRIPTION_COURS ($p) {
		return calculer_balise_dynamique($p, 'FORMULAIRE_INSCRIPTION_COURS', array());
	}

	//function balise_FORMULAIRE_INSCRIPTION_ACTIVITE_stat($args, $filtres) {
		// Si le moteur n'est pas active, pas de balise
		//if ($GLOBALS['meta']["activer_moteur"] != "oui")
			//return '';
		
		// filtres[0] doit etre un script (a revoir)
		//else
		  //return array($filtres[0], $args[0]);
	//}
	 
	// Balise de traitement des donn�es du formulaire
	function balise_FORMULAIRE_INSCRIPTION_COURS_dyn() {
		
		//On r�cup�re les champs
		$id_evenement=_request('id_evenement');
		$nom=_request('nom');
		$id_adherent=_request('id_adherent');
		$membre=_request('membres');
		$non_membre=_request('non_membres');
		$inscrits=_request('inscrits');
		$email=_request('email');
		$telephone=_request('telephone');
		$adresse=_request('adresse');
		$montant=_request('montant_cours');
		$montant_membre=_request('montant_membre');
		$commentaire=_request('commentaire');
		$statut=_request('statut');
		$membre=_request('membre');
		$type=_request('type');
		$statut=_request('statut');
		$id_auteur=_request('id_auteur');
		$statut_provisoire=_request('statut_provisoire');
		$url_retour=('spip.php?page=paiement');
		$retour_type=('&type=').$type;
		$retour_auteur=('&id_auteur=').$id_auteur;		
		$retour_statut=('&statut=paiement');
		$location=$url_retour.$retour_type.$retour_auteur;
		$compte=$GLOBALS['association_metas']['compte'];

		
		if ($statut=='confirmer'){

			$query = spip_query( " SELECT * FROM spip_evenements WHERE id_evenement=$id_evenement " );
			while ($data = spip_fetch_array($query)) {
				$prix_membres= $data['prix_membres']; 
				$prix_non_membres= $data['prix_non_membres']; 
				$prix_prevente_non_membres= $data['prix_prevente_non_membres'];
				if ($montant==$prix_membres){
				$type_paiement = "periode1";
				}
				elseif ($montant==$prix_non_membres){
				$type_paiement = "periode2";
				}
				elseif ($montant==$prix_prevente_non_membres){
				$type_paiement = "annee";
				};
			}		
			//enregistrement dans la table
			$valeurs= array(
						'id_evenement' => $id_evenement,
						'id_adherent' => $id_adherent,					
						'nom' => $nom,
						'membres' => $membres,
						'non_membres' => $non_membres,
						'inscrits' => $inscrits,								
						'email' => $email,
						'adresse' => $adresse,
						'telephone' => $telephone,
						'montant' => $montant,
						'commentaire' => $commentaire,
						'type_paiement' => $type_paiement,				
						'date' => 'NOW()');
			
			$n = sql_insertq('spip_asso_activites',$valeurs); 
			
			spip_log($n ? "enregistre activite $n" : "actvite non inseree");
			


			spip_query("UPDATE spip_auteurs_elargis SET montant="._q($montant_membre)." WHERE id_auteur=".$id_auteur); 	

			
			//on envoit des emails
			$nom_asso=$GLOBALS['association_metas']['nom'];
			$email_asso=$GLOBALS['association_metas']['email'];					
			$expediteur=$nom_asso.'<'.$email_asso.'>';		
			$entete .= "Reply-To: ".$email_asso."\n";     					 // r�ponse automatique � Association
			$entete .= "MIME-Version: 1.0\n";
			$entete .= "Content-Type: text/plain; charset=$charset\n";	// Type Mime pour un message au format HTML
			$entete .= "Content-Transfer-Encoding: 8bit\n";
			$entete .= "X-Mailer: PHP/" . phpversion();         			// mailer
			//$entetes.= "Content-Type: text/html; charset=iso-8859-1\n"; 
			//$entetes.= "X-Sender: < ".$data['mail'].">\n";   } 
			//$entetes .= "X-Priority: 1\n";                							// Message urgent ! 
			//$entetes .= "X-MSMail-Priority: High\n";         					// d�finition de la priorit�
			//$entetes .= "Return-Path: < webmaster@ >\n"; 					// En cas d' erreurs 
			//$entetes .= "Errors-To: < webmaster@ >\n";    					// En cas d' erreurs 
			//$entetes .= "cc:  \n"; 											// envoi en copie � �
			//$entetes .= "bcc: \n";          										// envoi en copie cach�e � �
			$sujet=_T('asso:activite_message_sujet',array('nomasso'=>$nom_asso));
			
			$query = spip_query( " SELECT * FROM spip_evenements WHERE id_evenement=$id_evenement " );
			while ($data = spip_fetch_array($query)) {
				$activite=$data['titre'];
				$date=$data['date_debut'];

			}

			$query2 = spip_query( " SELECT id_mot FROM spip_mots_evenements WHERE id_evenement=$id_evenement " );
			while ($data2 = spip_fetch_array($query2)) {
				$id_mot= $data2['id_mot']; 
				$query3 = spip_query( " SELECT * FROM spip_mots WHERE id_mot=$id_mot AND id_groupe=11" );
				while ($data3 = spip_fetch_array($query3)) {
				$lieu = $data3['titre'];
			}

			}
			
			//au webmaster
			$message = _T('asso:activite_message_webmaster',array(
				'nom' => $nom, 
				'activite' => $activite, 
				'inscrits' => $inscrits, 
				'commentaire'=>$commentaire,
				'id_asso'=>$id_adherent,				
				'compte'=>$compte
			));

			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail( $email_asso, $sujet, $message, $from = $expediteur, $headers = $entetes );
			
			$inscriptions=recuperer_fond('inc/participants',$valeurs);
			
			//au demandeur
			$adresse= $email;
			$message= _T('asso:cours_message_confirmation_inscription',array(
				'activite'=>$activite,
				'date'=>association_datefr($date),
				'lieu'=>$lieu,
				'nom'=>$nom,
				'id_adherent'=>$id_adherent,
				'id_asso'=>$id_adherent,	
				'membres'=>$membres,
				'non_membres'=>$non_membres,
				'inscriptions'=>$inscriptions,
				'email'=>$email,
				'telephone'=>$telephone,
				'adresse'=>$adresse,
				'montant'=>$montant,
				'commentaire'=>$commentaire,
				'nomasso'=>$nom_asso,
				'compte'=>$compte,
			));
			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail( $adresse, $sujet, $message, $from = $expediteur, $headers = $entetes );	
			$return = array('message'=>'message_ok');
			

					
			return _T('asso:cours_message_confirmation_inscription_html',array(
				'activite'=>$activite,
				'date'=>association_datefr($date),
				'lieu'=>$lieu,
				'nom'=>$nom,
				'id_adherent'=>$id_adherent,
				'membres'=>$membres,
				'non_membres'=>$non_membres,
				'inscriptions'=>$inscriptions,
				'email'=>$email,
				'telephone'=>$telephone,
				'adresse'=>$adresse,
				'montant'=>$montant,
				'commentaire'=>$commentaire,
				'nomasso'=>$nom_asso,
				'id_asso'=>$id_adherent,
				'compte'=>$compte,
			));
		}
		else {
			if ($statut=='Soumettre'){
				
				//On contr�le les donn�es du formulaire			
				$statut='Confirmer';	 // si pas d'erreur
				
				//email invalide
				if ( $email != email_valide($email) || empty($email) ){
					$erreur_email='Adresse courriel invalide !';
					$bouton='Soumettre';
				}
				//donnees manquantes
				if ( empty($nom) ){
					$erreur_nom='Nom et pr�nom manquants !';
					$bouton='Soumettre';
				}
				if ( empty($inscrits) ){
					$erreur_inscrits='Nombre d\'inscrits manquant !';
					$bouton='Soumettre';
				}
				
				//on retourne les infos � un formulaire de previsualisation		
				return inclure_balise_dynamique(
					array(
						'formulaires/formulaire_inscription_cours_previsu',0,
						array(
							'id_evenement'=> $id_evenement,
							'nom'		=> $nom,
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
							'erreur_email' => $erreur_email,
							'erreur_nom' => $erreur_nom,
							'erreur_inscrits' => $erreur_inscrits,
							'erreur_montant' => $erreur_montant,
						)
					),
					false
				);
			}
		}		
		
		//On retourne au formulaire d'inscription
		return array (
			'formulaires/formulaire_inscription_cours',0, 
			array (
				'id_evenement'=> $id_evenement,
				'nom'		=> $nom,
				'id_adherent'=> $id_adherent,
				'membres'=> $membres,
				'non_membres'=> $non_membres,
				'inscrits'	=> $inscrits,
				'email'		=> $email,
				'telephone'	=> $telephone,
				'adresse'=> $adresse,
				'montant' => $montant,
				'commentaire'=> $commentaire,
				'membre'=> $membre,
				'type'=>$type,
				'statut_provisoire'=>$statut_provisoire,
				)
			);
		
	}
?>