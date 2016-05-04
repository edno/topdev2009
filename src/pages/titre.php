<?php

require_once './sql/cnx_sql.php';
require_once './lib/search_lib.php';
require_once './lib/titre_lib.php';
require_once './tpl/titre_tpl.php';
require_once './lib/couple_lib.php';

if(!isset($deleteCouplage))$deleteCouplage='';

switch($action)
{
	// ajout d'un titre et couplage avec interprete
	case 'add':
	case 1:
	global $cfgAnneeTitreMin;
	global $cfgAnneeTitreMax;
	TitreFormulaire('');
	if($form_interprete_id0 == 0) // si aucun interprete n'a été choisi
	{
		echo '<div class="box-erreur"> Interperete non spécifié - paramètre obligatoire</div>';
	}
	// si l'année saisie ne correpond pas aux critères définis
	elseif($formAnnee != '' AND ($formAnnee < $cfgAnneeTitreMin OR $formAnnee > $cfgAnneeTitreMax))
	{
		echo '<div class="box-erreur">La date doit être comprise entre ' . $cfgAnneeTitreMin . ' et ' . $cfgAnneeTitreMax . '.</div>';
	}
	//sinon - insertion et mise à jour du couple
	else
	{
		$idTitre = TitreAjouter($formTitre, $formAnnee);
		CoupleAjouterID($idTitre, $form_interprete_id0, 1);
	}
	TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres));
	TitreRechercher('');
	break;
	
	//modification d'un titre
	case 'mod':
	case 2:
	// recupération des infos du titre à modifier
	$arTitre = 	RechercheLireCorrespondancesID($idTitre);
	// idem si le titre n'a pas d'interprete associé
	if(empty($arTitre)){$arTitre = TitreInfosID($idTitre);}
	// transmission au formulaire
	TitreFormulaire($arTitre);
	break;
	
	case 'updatecpl':
	case 3:
	// mise a jour du titre
	TitreMaj($idTitre, $formTitre, $formAnnee); 
	// mise à jour du couple et suppression des couples si demandé
	TitreMajCouple($idTitre, $idOldCouple, $idNewCouple, $deleteCouplage);
	// ajout de couples si demandé
	if($form_interprete_id0 != 0)
	{
		CoupleAjouterID($idTitre, $form_interprete_id0, 0);
	}
	TitreFormulaire('');
	TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres));
	TitreRechercher('');
	break;
	
	case 'search':
	case 4:
	TitreFormulaire('');
	// si la recherche est trop courte affichage des x derniers chargés
	if(strlen($formSearchTitre) < 2 AND !empty($formSearchTitre))
	{
		echo '<div class="box-erreur">La recherche doit comporter 2 caractères minimum.</div>';
		TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres), 0);
	}
	else
		{// recherche de la chaine demandée
		$rechercheTitre = TitreAssocierInterpreteOriginal($formSearchTitre);
		if(empty($rechercheTitre))// si aucun résultat, affichage des x dernier chargés
		{
			echo '<div class="box-erreur">Aucun résultat trouvé pour ' . $formSearchTitre . '.</div>';
			TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres), 0);
		}
		else // sinon affichage des resultats de la recherche
		{
			TitreAfficher($rechercheTitre);
		}
	}
	TitreRechercher('');
	break;
	
	case 'delete':
	case 5:
	// suppression d'un titre et de ses couples
	TitreSupprimer($idTitre);
	TitreFormulaire('');
	TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres));
	TitreRechercher('');
	break;

	case 'show-add':
	case 0:
	default:
	// afichage par défaut
	TitreFormulaire('');
	TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres));
	TitreRechercher('');
	break;
}
?>