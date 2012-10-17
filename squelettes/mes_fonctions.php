<?php
/* Fonction qui perment de recadrer et reduire les images */
function image_reduire_recadre($img, $largeur, $hauteur, $position='center') {
       include_spip('inc/filtres_images');
       if ($img!='IMG/'){
            list ($ret["hauteur"],$ret["largeur"]) = taille_image($img);
            $ratio_x = $ret["largeur"]/$largeur;
            $ratio_y = $ret["hauteur"]/$hauteur;
            $ratio   = ($ratio_x <= $ratio_y) ? $ratio_x : $ratio_y;
            return image_recadre(image_reduire_par($img, $ratio), $largeur, $hauteur, $position);
            }
}

/* Fonction qui perment de recadrer et reduire les images */
function image_reduire_recadre_logo($img, $largeur, $hauteur, $position='center') {
       include_spip('inc/filtres_images');
        $img = 'IMG/'.$img;
       if ($img!='IMG/'){
            list ($ret["hauteur"],$ret["largeur"]) = taille_image($img);
            $ratio_x = $ret["largeur"]/$largeur;
            $ratio_y = $ret["hauteur"]/$hauteur;
            $ratio   = ($ratio_x <= $ratio_y) ? $ratio_x : $ratio_y;
            return image_recadre(image_reduire_par($img, $ratio), $largeur, $hauteur, $position);
            }
}
/* Fonction qui perment de modifier les jours de la date actuelle */
function modifier_jours($date,$modification,$format){
	$date=date($format, strtotime ($date."$modification day"));
	return $date;
}

/* Cherche le titre le titre de l'article */
function nom_article($id_article){

	$titre=sql_getfetsel('titre','spip_articles','id_article='.sql_quote($id_article).' AND id_trad='.sql_quote($id_article).' AND lang='.sql_quote(_request('lang')));
	
	if(!$titre){
		$donnees=sql_fetsel('id_trad,titre','spip_articles','id_article='.sql_quote($id_article));
		if($donnees['id_trad']==0)$titre=$donnees['titre'];
		else{
			$titre=sql_getfetsel('titre','spip_articles','id_trad='.sql_quote($donnees['id_trad']).' AND lang='.sql_quote(_request('lang')));
			if(!$titre)$titre=sql_getfetsel('titre','spip_articles','id_trad='.sql_quote($donnees['id_trad']).' AND id_article='.sql_quote($donnees['id_trad']));	
			}
		}
	return propre($titre);
}

//groupe les evenements d'un article par jour de la semaine
function nombres_jours($id_article){

	$sql=sql_select('date_debut,id_evenement','spip_evenements','id_article='.sql_quote($id_article).' AND id_evenement_source=0');
	
	$jours=array();
	$evenements=array();
	while($data=sql_fetch($sql)){
		$jours[date('w',strtotime($data['date_debut']))]=$data['id_evenement'];
		$evenements[$data['date_debut']]=$data['id_evenement'];		
		}
	$evenements_jours=array();	
	foreach($jours AS $jour=>$ev){
		$evenements_jours[$jour]=array();
		}
	//	echo serialize($evenements_jours);
	foreach($evenements AS $jour=>$ev){
		if(array_key_exists(date('w',strtotime($jour)),$jours)){
			$evenements_jours[date('w',strtotime($jour))][]=$ev;
			}
		}
		
return $evenements_jours;
}

//Restreindre l'affichage de certain,s champs extras
include_spip('inc/cextras_autoriser'); 

// restreindre le champ 'prix_non_membres','prix_membres'des articles, sur les rubriques festivals

if (function_exists('restreindre_extras'))restreindre_extras('article', array('prix_non_membres','prix_membres','inscription_active'), array(168, 169, 170));
?>
