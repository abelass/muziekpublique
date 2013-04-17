<?php

/**
 * Action d'export CSV des auteurs
 * 
 * Plugin Inscription 2 Import / Export
 * 
 */
function action_export_festival_dist(){
//	$securiser_action = charger_fonction('securiser_action', 'inc');
//	$arg = $securiser_action();
	
	include_spip('inc/autoriser');
	include_spip('inc/i2_import');
		$delim = ',';

		
		/**
		 * RÃ©cupÃ©ration des champs Ã  exporter
		 */
		 
		$tables = 'spip_asso_activites';
		$tablefield=i2_import_table_fields($tables);


			/**
			* Export des tables mergées
			*/

		$output=i2_import_csv_ligne($tablefield,$delim);
		
		// ne fonctionne pas a verifier pourquoi, du coup m'id est inclus en dur
		$id_article=_request('arg');


		$result = sql_select($tablefield,'spip_asso_activites','id_article='._request('arg'));



			$f=array();
			while ($row=sql_fetch($result)){
				if(!in_array($row['id_evenement'],$f)){
				//$f[]=$row['id_evenement'];	
				$ligne=array();
				
				foreach($tablefield as $key)
				  if (isset($row[$key])){
					if($key=='id_evenement' AND $row['id_evenement']!=0)$ligne[]=extraire_multi(sql_getfetsel('titre','spip_evenements','id_evenement='.$row['id_evenement'])).' heure : '.$row['heure'];	
				    else$ligne[]=$row[$key];
				}
					else
					  $ligne[]="";
					$output .= i2_import_csv_ligne($ligne,$delim); 
				}					  
				
			}
			
			/*while ($row=sql_fetch($result)){
				$ligne=array();
				foreach($tablefield as $key)
				  if (isset($row[$key]))
				    $ligne[]=$row[$key];
					else
					  $ligne[]="";
				$output .= i2_import_csv_ligne($ligne,$delim);
			}	*/

		$charset = $GLOBALS['meta']['charset'];
		
		$nom = sql_getfetsel('titre','spip_article','id_article='.sql_quote($id_article));
	
		include_spip('inc/texte');
		$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo(_T('export_activite').' - '.$nom))));
	
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

		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
		Header("Content-Length: ".strlen($output));
		echo $output;
		exit;
	
	return;
}
?>
