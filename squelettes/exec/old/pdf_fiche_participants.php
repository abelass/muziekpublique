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
		$this->SetFont('Arial','',8);
		$this->Cell(0,6,'Association '.$association,0,1,'L');
		$this->SetFont('Arial','B',10);
		$this->Cell(0,6,'Fiche Cours :',0,1,'C');
		$this->Ln(10);
		//Imprime l'en-t�te du tableau si n�cessaire
		parent::Header();
	}
}

$pdf=new PDF();	

$pdf->Open();
$pdf->AddPage();
//On définit les colonnes (champs,largeur,intitulé,alignement)
$pdf->AddCol('nom',50,_T('asso:activite_libelle_nomcomplet'),'L');
$pdf->AddCol('id_adherent',20,'Nr membre','R');
$sql=spip_query ("SELECT * FROM spip_evenements WHERE id_evenement_source=$id_evenement ORDER BY date_debut ASC LIMIT 8");
while ($data= spip_fetch_array($sql)){
			$date_debut=$data['date_debut'];
$pdf->AddCol('date_debut',20,affdate($date_debut,'d-m-Y'),'L');
		}
$pdf->AddCol('montant',15,'Montant','R');
$pdf->AddCol('statut',15,'Statut','L');
$prop=array(
		'HeaderColor'=>array(255,150,100),
          'color1'=>array(224,235,255),
          'color2'=>array(255,255,255),
          'padding'=>2);
$pdf->Table("SELECT * FROM spip_asso_activites WHERE id_evenement=$id_evenement ORDER BY nom",$prop);
$pdf->Output();
?>
