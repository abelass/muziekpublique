<?php
		
	include ('.././inc/version.php');
	include ('envoyer_mail.php');


	function envoyer_mail($email, $sujet, $texte, $from = "", $headers = "") {
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	return $envoyer_mail($email,$sujet,$texte,$from,$headers);
}
		$url_action_relances = 'envoi_email.php';
		$url_retour=$_POST['url_retour'];
		
		//On r�cup�re les donn�es globales
		$action=$_REQUEST['action'];
		$sujet=$_POST['sujet'];
		$message=$_POST['message'] ;
		$statut=$_POST['statut'];
		$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
		$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$count=count ($email_tab);

echo $_POST["email"];
echo $action;		
		// CONFIRMATION
		if ($action=="confirm") {


			echo '<p><strong> Vous vous appr&ecirc;tez &agrave; envoyer '.$count;
			if ($count==1)
			{ echo ' relance';}
			else
			{ echo ' relances';}
			echo '</strong></p>';
			echo '<p>'.$sujet.'</p>';
			echo '<fieldset>';
			echo nl2br($message);
			echo '</fieldset>';
			
			echo '<form method="post" action="'.$url_action_relances.'">';
			for ( $i=0 ; $i < $count ; $i++ ) {
				echo '<input name="id[]" type="hidden" value="'.$id_tab[$i].'">';
				echo '<input name="statut[]" type="hidden" value="'.$statut_tab[$i].'">';
				echo '<input name="email[]" type="hidden" value="'.$email_tab[$i].'">';
			}
			echo '<input name="sujet" type="hidden" value="'.$sujet.'">';
			echo '<input name="message" type="hidden" value="'.$message.'">';
			echo '<input name="action" type="hidden" value="send">';
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<div style="float:right;"><input name="submit" type="submit" value="Confirm" class="fondo" /></div>';
			echo '</form>';	
			//remettre le champ 0 �� 1 et r�actualiser la date
			//spip_query("UPDATE spip_auteurs_elargis SET regle_le='relance',date_jour=NOW() WHERE id_ad=$id");	

			exit;
		}
		
		//ENVOI
		if ($action=="send") {
			//On pr�pare le mail et on envoi! On peut modifier le $headers �� sa guise
			$nomasso='Muziekpublique';
			$adresse='abelass@gmx.net';
			$expediteur=$nomasso.'<'.$adresse.'>'; 
			$expediteur=$nomasso.'<'.$adresse.'>';      					//exp�diteur Association
			//$entetes .= "X-Mailer: PHP/" . phpversion();         			// mailer
			//$entetes .= "X-Sender: < ".$adresse.">\n";
			//$entetes .= "X-Priority: 1\n";                					// Message urgent ! 
			//$entetes .= "X-MSMail-Priority: High\n";        				// d�finition de la priorit�
			//$entetes .= "Return-Path: <".$adresse.">\n"; 									// En cas d' erreurs 
			//$entetes .= "Errors-To: < ".$adresse.">\n";    									// En cas d' erreurs 
			//$entetes .= "cc:  \n"; 													// envoi en copie �
			//$entetes .= "bcc: \n";          												// envoi en copie cach�e �
			
			//On envoit le mail aux destinataires s�lectionn�s et on change le statut de relance
			
			for ( $i=0 ; $i < $count ; $i++ ) {
				$email = $email_tab[$i];
				$statut = $statut_tab[$i];
				$id = $id_tab[$i];
				
				if ( isset ( $id ) ) {
					envoyer_mail ( $email, $sujet, $message, $expediteur, "");
					if ($statut=="echu"){
						spip_query("UPDATE spip_auteurs_elargis SET statut_interne='relance' WHERE id_auteur = '$id' ");
					}
				}
			}
			header ('location:'.$url_retour);
			exit;
		}
	
?>
