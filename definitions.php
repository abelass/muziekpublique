<?php

function import_table_fields($tables,$type='unique'){
	$trouver_table = charger_fonction('trouver_table','base');
	$table_fields_final = array();
	if(is_array($tables)){
		foreach($tables as $table){
			if($table == 'spip_auteurs'){
				// Tous les champs de spip_auteurs ne sont pas à prendre en compte
				$spip_auteurs['id_auteur'] = 'id_auteur';				
				$spip_auteurs['nom'] = 'nom';
				//$spip_auteurs['bio'] = 'bio';
				$spip_auteurs['email'] = 'email';
				//$spip_auteurs['nom_site'] = 'nom_site';
				//$spip_auteurs['url_site'] = 'url_site';
				//$spip_auteurs['login'] = 'login';
				$spip_auteurs['statut'] = 'statut';
			}else{
				$table_desc = $trouver_table($table);
				$spip_auteurs_elargis=array_keys(is_array($table_desc['field']) ? $table_desc['field'] : array());
				$spip_auteurs_elargis=array_flip($spip_auteurs_elargis);
				foreach ($spip_auteurs_elargis as $key=>$value) {
					/**
					 * On ne garde que les champs activés
					 */
					if(lire_config('inscription2/'.$key) == 'on'){
						$spip_auteurs_elargis[$key] = $key;
					}else{
						unset($spip_auteurs_elargis[$key]);
					}
				}
				// On ne met pas à disposition le champs id_auteur
				unset($spip_auteurs_elargis['id_auteur']);
			}
		}
		if($type == 'unique'){
			$table_fields_final = array_merge($spip_auteurs,$spip_auteurs_elargis);
			return $table_fields_final;
		}
		else{
			return array($spip_auteurs,$spip_auteurs_elargis);
		}
	}
	return;
}

function concordonace_champs($objet){
	$champs=array();

	switch($objet){

	case 'auteur':
		$tables = array('spip_auteurs','spip_auteurs_elargis');	

		$champs['tablefields']=import_table_fields($tables,$type='unique',$objet);
	
		$champs['titre_concordance']=array(
			'id_auteur'=>'trad',
			'nom'=>'nom2',	
			'email'=>'email',
			'statut'=>'statut',					
			'nom_famille'=>'nom_famille',
			'prenom'=>'prenom',	
			'adresse'=>'adresse',
			'code_postal'=>'code_postal',								
			'ville'=>'ville',
			'telephone'=>'telephone',	
			'naissance'=>'naissance',
			'sexe'=>'sexe',					
			'pays'=>'pays',
			'mobile'=>'mobile',	
			'telephone_pro'=>'telephone_pro',
			'creation'=>'creation',										
			'domaine_travail'=>'domaine_travail',
			'etat_civil'=>'etat_civil',	
			'enfants'=>'enfants',
			'enfants_annees_naissance'=>'enfants_annees_naissance',					
			'petits_enfants'=>'petits_enfants',
			'animal_domestique'=>'animal_domestique',	
			'fumer'=>'fumer',
			'voiture'=>'voiture',								
			'vecu_belgique'=>'vecu_belgique',
			'pays_origine'=>'pays_origine',	
			'statut_professionel'=>'statut_professionel',
			'profession_secteur'=>'profession_secteur',					
			'comment_connu'=>'comment_connu',
			'diplomes_utilisateur'=>'diplomes_utilisateur',	
			'diplomes_conjoint'=>'diplomes_conjoint',
			'ville_etude'=>'ville_etude',						
			'libre_quand'=>'libre_quand',
			'langues'=>'langues',	
			'opt_in'=>'opt_in',						
			);
			
			foreach($champs['tablefields'] as $key=>$value){
				if(!$champs['titre_concordance'][$key])$champs['titre_concordance'][$key]=$value;
				}		
		break;

		}
		return $champs;
	}
?>