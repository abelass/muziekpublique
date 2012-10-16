<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('pdf/extends');

function exec_pdf_concert()
{
	if (!autoriser('configurer')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

	$id_evenement=intval($_GET['id']);
	
	$champs = array('titre', 'id_article', 'id_rubrique', 'statut');

	$where = array('id_evenement='.$id_evenement);


	$result = sql_select('titre', "spip_evenements", $where);
	
	while($row = sql_fetch($result)){
		$titre=$row['titre'];
		};
	



	$pdf=new PDF();	
	$pdf->titre = _T('asso:activite_titre_inscriptions_activites').' concert : '. $titre ;
	$pdf->Open();
	$pdf->AddPage();
	//On d�finit les colonnes (champs,largeur,intitul�,alignement)
	$pdf->AddCol('id_activite',10,'ID','R');
	$pdf->AddCol('nom',50,_T('asso:activite_libelle_nomcomplet'),'L');
	$pdf->AddCol('id_adherent',20,'Nr membre','R');
	$pdf->AddCol('membres',20,'Membre','L');
	$pdf->AddCol('non_membres',20,'N-membre','L');
	$pdf->AddCol('prevente_non_membre',25,'PVN-membr','L');
	$pdf->AddCol('prix_ext_1_valeur',10,'P1','L');
	$pdf->AddCol('prix_ext_2_valeur',10,'P2','L');	
	$pdf->AddCol('prix_ext_3_valeur',10,'P3','L');				
	$pdf->AddCol('inscrits',15,'Nbre','R');
	$pdf->AddCol('montant',20,'Montant','R');
	$pdf->AddCol('statut',10,'Statut','L');
	$prop=array(
		'HeaderColor'=>array(255,150,100),
          'color1'=>array(224,235,255),
          'color2'=>array(255,255,255),
          'padding'=>2);
	$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom",$prop);
	$pdf->Output();
	}
}
?>
