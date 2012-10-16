<?php

define('FPDF_FONTPATH','font/');
include_spip('pdf/pdf_table');

$id_evenement=$_GET['id'];
$association=lire_config('association/nom');

class PDF extends PDF_Table {

	function PDF(){
		$this->FPDF('L', 'mm', 'A4');
	}
	
	function Header(){
		//Titre
		$this->SetFont('Arial','',10);
		$this->Cell(0,6,'Association '.$association,0,1,'L');
		$this->SetFont('Arial','B',14);
		$this->Cell(0,6,_T('asso:activite_titre_inscriptions_activites'),0,1,'C');
		$this->Ln(10);
		//Imprime l'en-t�te du tableau si n�cessaire
		parent::Header();
	}
}

$pdf=new PDF();	

$pdf->Open();
$pdf->AddPage();
//On d�finit les colonnes (champs,largeur,intitul�,alignement)
$pdf->AddCol('id_activite',10,'ID','R');
$pdf->AddCol('nom',50,_T('asso:activite_libelle_nomcomplet'),'L');
$pdf->AddCol('id_adherent',20,'Nr membre','R');


$sql=spip_query ("SELECT * FROM spip_evenements WHERE id_evenement=".$id_evenement);
	while ($data= spip_fetch_array($sql)){
			$id_article=$data['id_article'];
		$sql2=spip_query ("SELECT * FROM spip_articles WHERE id_article=".$id_article);
	while ($id= spip_fetch_array($sql2)){
			$id_rubrique=$id['id_rubrique'];
			}
		}
if ($id_rubrique==8 or $id_rubrique==26 or $id_rubrique==39){
$pdf->AddCol('membres',15,'Membres','L');
$pdf->AddCol('non_membres',25,'Non membres','L');
	}
$pdf->AddCol('inscrits',20,'Nbre','R');
$pdf->AddCol('montant',20,'Montant','R');
$pdf->AddCol('statut',15,'Statut','L');
$prop=array(
		'HeaderColor'=>array(255,150,100),
          'color1'=>array(224,235,255),
          'color2'=>array(255,255,255),
          'padding'=>2);
$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom",$prop);
$pdf->Output();
?>
