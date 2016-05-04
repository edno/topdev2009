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
	if($form_interprete_id0 == 0) // si aucun interprete n'a �t� choisi
	{
		echo '<div class="box-erreur"> Interperete non sp�cifi� - param�tre obligatoire</div>';
	}
	// si l'ann�e saisie ne correpond pas aux crit�res d�finis
	elseif($formAnnee != '' AND ($formAnnee < $cfgAnneeTitreMin OR $formAnnee > $cfgAnneeTitreMax))
	{
		echo '<div class="box-erreur">La date doit �tre comprise entre ' . $cfgAnneeTitreMin . ' et ' . $cfgAnneeTitreMax . '.</div>';
	}
	//sinon - insertion et mise � jour du couple
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
	// recup�ration des infos du titre � modifier
	$arTitre = 	RechercheLireCorrespondancesID($idTitre);
	// idem si le titre n'a pas d'interprete associ�
	if(empty($arTitre)){$arTitre = TitreInfosID($idTitre);}
	// transmission au formulaire
	TitreFormulaire($arTitre);
	break;
	
	case 'updatecpl':
	case 3:
	// mise a jour du titre
	TitreMaj($idTitre, $formTitre, $formAnnee); 
	// mise � jour du couple et suppression des couples si demand�
	TitreMajCouple($idTitre, $idOldCouple, $idNewCouple, $deleteCouplage);
	// ajout de couples si demand�
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
	// si la recherche est trop courte affichage des x derniers charg�s
	if(strlen($formSearchTitre) < 2 AND !empty($formSearchTitre))
	{
		echo '<div class="box-erreur">La recherche doit comporter 2 caract�res minimum.</div>';
		TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres), 0);
	}
	else
		{// recherche de la chaine demand�e
		$rechercheTitre = TitreAssocierInterpreteOriginal($formSearchTitre);
		if(empty($rechercheTitre))// si aucun r�sultat, affichage des x dernier charg�s
		{
			echo '<div class="box-erreur">Aucun r�sultat trouv� pour ' . $formSearchTitre . '.</div>';
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
	// afichage par d�faut
	TitreFormulaire('');
	TitreAfficher(TitreAssocierInterpreteOriginal('', $cfgnTopTitres));
	TitreRechercher('');
	break;
}
?>