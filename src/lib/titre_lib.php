<?php
/**
 * Gestion des titres
 * Bibliothèque de fonctions
 *
 * @author Damien Mathieu
 */

require_once './lib/common_lib.php';
require_once './lib/couple_lib.php';
require_once './lib/search_lib.php';

/**
* 
* Fonction d'ajout de titre
* 
* @Param $ajoutTitre Titre à insérer
* @Param $ajoutAnnee Année du titre
* @Return id du titre inséré si ok, sinon infos du titre déjà en base array(id, titre année)
*/

function TitreAjouter($ajoutTitre, $ajoutAnnee=0)
{
	global $mysql_data;
	
	if( is_null($ajoutTitre) === true || 
	strlen(trim($ajoutTitre)) == 0) return false;
	
	// controle de l'unicite du titre
	$TitreExiste = RechercheLireTitres($ajoutTitre, 0, false, true);
	// si titre unique - insertion et renvoi de son ID
	if (!$TitreExiste)
	{
		$strQuery = "INSERT INTO " . $mysql_data['tables']['titres'] . " (titreTitre, anneeTitre)
					VALUES ('".$ajoutTitre."','".$ajoutAnnee."')";
		$hResult = mysql_query($strQuery);
		if(!$hResult) return false;
		return mysql_insert_id();
	}
	else
	// sinon renvoi des infos du titre déja en base
	{
		return $TitreExiste[0]['idTitre'];
	}
}

/**
* 
* Fonction de récupération des infos pour modification d'un titre
* 
* @Param $idTitre ID du titre à modifier
* @Return array multiple contenant les infos du titre à modifier (id, titre, année, nom interprete, prenom interprete, artiste original)
*/

function TitreAssocierInterpreteOriginal($strTitres, $cfgnTopTitres=0)
{
	$n = 0;
	$arTitres = RechercheLireTitres($strTitres, $cfgnTopTitres);
	
	if(is_null($arTitres) === true || 
	strlen(trim($arTitres)) == 0) return false;

	foreach($arTitres as $arTitre)
	{
		$titreOriginal = RechercheLireTitreInterpretes($arTitre['titreTitre'], true);
		if (empty($titreOriginal))
		{
			$fullName = 'N.D.';
		}
		else
		{
			$fullName = $titreOriginal[0]['prenomInterprete'] . ' ' . $titreOriginal[0]['nomInterprete'];
		}
		$arTitres[$n]['interpreteOriTitre'] = $fullName;
		$n++;
	}
	return $arTitres;
}

/**
* 
* Fonction de mise à jour des informations relatives à un titre
* 
* @Param $idTitre Id du titre concerné par les modifications
* @Param $idOldCouple Id du couple avant modification
* @Param $idNewCouple Id du nouveau couple souhaité
* @Param $deleteCouplage array contenant les ID des couples à supprimer
* 
*/
	
function TitreMajCouple($idTitre, $idOldCouple, $idNewCouple, $deleteCouplage)
{	
	global $mysql_data;
	// s'il y a pas de modification de l'interprete principal
	if($idOldCouple != $idNewCouple)
		{
		// mise a zero de interpreteOriginal sur l'ancien couple
		$strQuery = "UPDATE ".$mysql_data['tables']['couples']." SET interpreteOriginal='0' WHERE idCouple='".$idOldCouple."'";
		mysql_query($strQuery);
		// mise a 1 de interpreteOriginal sur le nouveau couple
		$strQuery = "UPDATE ".$mysql_data['tables']['couples']." SET interpreteOriginal='1' WHERE idCouple='".$idNewCouple."'";
		mysql_query($strQuery);
		}
	
	// si des demandes de suppression de couple ont été soumises
	if (!empty($deleteCouplage))
	{
		// parcours de l'array contenant les demandes
		foreach($deleteCouplage as $delID => $delMark)
		{
		// on vérifie que le couple n'a pas été passé avec le marqueur interprete original à l'étape précédente
		$strQuery = "SELECT interpreteOriginal FROM ".$mysql_data['tables']['couples']." WHERE idCouple='".$delID."'";
		$hOriginal = mysql_query($strQuery);
		$isOriginal = mysql_fetch_array($hOriginal);
		// retour si le couple à le marqueur interpreteOriginal à 1
		if($isOriginal[0] == 1)
			{
			echo 'Impossible de supprimer un couple Interprète Original / Titre.';
			}
		// sinon, suppression du couple demandé
			else
			{
			$strQuery = "DELETE FROM ".$mysql_data['tables']['couples']." WHERE idCouple='".$delID."'";
			mysql_query($strQuery);
			}
		}
	}
}

/**
* 
* Fonction de mise à jour d'un titre
* 
* @Param $idTitre ID du titre à modifier
* @Param $formTitre nouveau titre à insérer
* @Param $formAnnee nouvelle année à insérer
*/

function TitreMaj($idTitre, $formTitre, $formAnnee)
{
	global $mysql_data;
	$strQuery = "UPDATE ".$mysql_data['tables']['titres']." SET titreTitre='".$formTitre."', anneeTitre='".$formAnnee."' WHERE idTitre='".$idTitre."'";
	mysql_query($strQuery);
}

/**
* 
* Fonction de récupération des infos pour un titre sans interprete depuis son ID
* 
* @Param $idTitre ID du titre
* @Return array multiple (format compatible avec les resultats issus des fonctions de recherche) contenant les infos du titre à modifier (id, titre, année)
*/

function TitreInfosID($idTitre)
{
	global $mysql_data;
	
	if( is_null($idTitre) === true || 
	strlen(trim($idTitre)) == 0) return false;
	//requete sur l'id du titre pour récupérer ses informations
	$strQuery = "SELECT * FROM ".$mysql_data['tables']['titres']." WHERE idTitre='".$idTitre."'";
	$hResult = mysql_query($strQuery);
	if(!$hResult) return false;
	//formatage du retour sur le même modele que les fonctions de recherche
	$retour = mysql_fetch_array($hResult);
	$titreInfos = array();
	array_push($titreInfos, $retour);
	return $titreInfos;
}

/**
* 
* Fonction de suppression d'un titre ET de tous les couples associés
* 
* @Param $idTitre ID du titre à supprimer
*/

function titreSupprimer($idTitre)
{
	global $mysql_data;
	
	if( is_null($idTitre) === true || 
	strlen(trim($idTitre)) == 0) return false;
	//suppression des couples concernant le titre
	$strQuery = "DELETE FROM ".$mysql_data['tables']['couples']." WHERE idTitre='".$idTitre."'";
	$hResult = mysql_query($strQuery);
	//suppression du titre
	$strQuery = "DELETE FROM ".$mysql_data['tables']['titres']." WHERE idTitre='".$idTitre."'";
	$hResult = mysql_query($strQuery);
}