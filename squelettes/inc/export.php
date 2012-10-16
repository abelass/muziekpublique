<?php
/**
		 * Récupération des champs à exporter
		 */
function exporter($objet='auteur',$type='print',$where=''){
	include_spip('inc/i2_import');
	$delim = $arg ? $arg : (_request('delim') ? _request('delim') : ',');		 
	include_spip('inc/definitions');	
	 
	
	switch($objet){
		case 'auteur' :	 
			$multi =array();	 
		
			$multi =array('domaine_travail','animal_domestique','ville_etude');
			$champs = concordonace_champs($objet);
			$champs_titre =$champs['titre_concordance'];
			$tablefield=$champs['tablefields'];
				
			$output=i2_import_csv_ligne($champs_titre,$delim);
	
		
				/**
				* Export des tables mergées
				*/
	
			$result = sql_select($tablefield,'spip_auteurs AS A LEFT JOIN spip_auteurs_elargis AS B USING(id_auteur)',$where,'','','','',false);

			while ($row=sql_fetch($result)){
				$ligne=array();
				foreach($champs_titre as $key=>$value)
					if (isset($row[$key])){
						if(in_array($key,$multi)){
							$val=unserialize($row[$key]);
							$v='';
							if(is_array($val)){
								foreach($val as $key=>$valeur){
									$v=$v.$valeur.',';
										}
								}
								$ligne[]=$v;
							} 
							else $ligne[]=$row[$key];
						}
						else $ligne[]="";
					$output .= i2_import_csv_ligne($ligne,$delim);
				}
			
				$charset = $GLOBALS['meta']['charset'];
			
				include_spip('inc/texte');
				$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo(_T('i2_import:export_users_sites',array('date' => date('Y-m-d'),'site'=>$GLOBALS['meta']['nom_site']))))));
			
				// Excel ?
				if ($delim == ',')
					$extension = 'csv';
				else {
					$extension = 'xls';
					# Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
					include_spip('inc/charsets');
					$output = unicode2charset(charset2unicode($output), 'iso-8859-1');
					$charset = 'iso-8859-1';
				}
		break;
		case 'reponses' : 
				$export_reponses=charger_fonction('exporter_formulaires_reponses_base','inc/');
				$output =$export_reponses($where);
				echo $output;	
		}
		
	switch ($type){
		case 'print':
		
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
		Header("Content-Length: ".strlen($output));
		echo $output;
		exit;

		break;
		case 'crono_save':
			echo ':'.count($ligne);
			if($output)return $output;
		break;
		}
	}
	return
?>		