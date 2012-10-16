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

$name = _request('name');
$paper_size  =_request('paper_size');
$marginLeft = _request('marginLeft');
$marginTop  = _request('marginTop');
$NX = _request('NX');
$NY = _request('NY');
$SpaceX = _request('SpaceX');
$SpaceY = _request('SpaceY');
$width = _request('width');
$height = _request('height');
$metric = _request('metric');
$font_size = _request('font_size');



	define('FPDF_FONTPATH','font/');
	include_spip('pdf/pdf_label');

	// Formatage des feuilles d'etiquettes
	$pdf = new PDF_Label(
		array(
			'name'=>$name, 	//Nom du format	
			'paper-size'=>$paper_size, 		//Format du support
			'marginLeft'=>$marginLeft, 		//Marge int�rieure gauche
			'marginTop'=>$marginTop, 		//Marge sup�rieure avant la premi�re �tiquette
			'NX'=>$NX, 					//Nombre de colonnes
			'NY'=>$NY, 					//Nombre de rang�es
			'SpaceX'=>$SpaceX, 			// Espace horizontal entre les �tiquettes
			'SpaceY'=>$SpaceY, 			//Espace vertical entre les �tiquettes
			'width'=>$width, 			//Largeur de l'�tiquette
			'height'=>$height, 				//Hauteur de l'�tiquette
			'metric'=>$metric, 			//Unit� de mesure
			'font-size'=>$font_size			//Taille de la police
		), 1, 1
	);

	$pdf->Open();
	//$pdf->AddPage();			//S'il reste une feuille entamee dans l'imprimante

	// On imprime les �tiquettes
	if ( isset( $_POST['label'] ) ) {
		$label_tab=(isset($_POST["label"])) ? $_POST["label"]:array(); 
		$count=count ($label_tab);
		
		for ( $i=0 ; $i < $count ; $i++ ) {
			$id = $label_tab[$i];
			$query=spip_query ( "SELECT id , nom_famille , prenom, adresse , code_postal , ville  FROM spip_auteurs_elargis WHERE id='$id' ORDER BY nom_famille, adresse" );
			//$query=spip_query ( "SELECT DISTINCT id_asso , nom_famille , adresse , code_postal , ville , IF(COUNT(adresse )=2 , 'M. et Mme' , IF(sexe ='F' , 'Mme' , 'M.' )) AS cher , IF(COUNT(adresse )=1 , prenom , '' ) AS prenom FROM spip_auteurs_elargis WHERE statut_interne <>'sorti' GROUP BY adressee  ORDER BY id_asso" );
			$data=spip_fetch_array($query);
			$adresse= $data['adresse'];
			$adresse = utf8_decode($adresse); 
			$nom_famille = $data['nom_famille'];
			$nom_famille = utf8_decode($nom_famille); 
			$prenom = $data['prenom'];
			$prenom = utf8_decode($prenom); 
			
			//Mise en page de l'etiquette
			$pdf->Add_PDF_Label(sprintf("%s\n\n%s\n%s\n%s",$id,''.$prenom.' '.$nom_famille, $adresse, $data['code_postal'].' '.$data['ville']));
		}
	}

	$pdf->Output();
?>
