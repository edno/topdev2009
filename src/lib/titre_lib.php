<?php
/**
 * Gestion des titres
 * Biblioth�que de fonctions
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
* @Param $ajoutTitre Titre � ins�rer
* @Param $ajoutAnnee Ann�e du titre
* @Return id du titre ins�r� si ok, sinon infos du titre d�j� en base array(id, titre ann�e)
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
	// sinon renvoi des infos du titre d�ja en base
	{
		return $TitreExiste[0]['idTitre'];
	}
}

/**
* 
* Fonction de r�cup�ration des infos pour modification d'un titre
* 
* @Param $idTitre ID du titre � modifier
* @Return array multiple contenant les infos du titre � modifier (id, titre, ann�e, nom interprete, prenom interprete, artiste original)
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
* Fonction de mise � jour des informations relatives � un titre
* 
* @Param $idTitre Id du titre concern� par les modifications
* @Param $idOldCouple Id du couple avant modification
* @Param $idNewCouple Id du nouveau couple souhait�
* @Param $deleteCouplage array contenant les ID des couples � supprimer
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
	
	// si des demandes de suppression de couple ont �t� soumises
	if (!empty($deleteCouplage))
	{
		// parcours de l'array contenant les demandes
		foreach($deleteCouplage as $delID => $delMark)
		{
		// on v�rifie que le couple n'a pas �t� pass� avec le marqueur interprete original � l'�tape pr�c�dente
		$strQuery = "SELECT interpreteOriginal FROM ".$mysql_data['tables']['couples']." WHERE idCouple='".$delID."'";
		$hOriginal = mysql_query($strQuery);
		$isOriginal = mysql_fetch_array($hOriginal);
		// retour si le couple � le marqueur interpreteOriginal � 1
		if($isOriginal[0] == 1)
			{
			echo 'Impossible de supprimer un couple Interpr�te Original / Titre.';
			}
		// sinon, suppression du couple demand�
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
* Fonction de mise � jour d'un titre
* 
* @Param $idTitre ID du titre � modifier
* @Param $formTitre nouveau titre � ins�rer
* @Param $formAnnee nouvelle ann�e � ins�rer
*/

function TitreMaj($idTitre, $formTitre, $formAnnee)
{
	global $mysql_data;
	$strQuery = "UPDATE ".$mysql_data['tables']['titres']." SET titreTitre='".$formTitre."', anneeTitre='".$formAnnee."' WHERE idTitre='".$idTitre."'";
	mysql_query($strQuery);
}

/**
* 
* Fonction de r�cup�ration des infos pour un titre sans interprete depuis son ID
* 
* @Param $idTitre ID du titre
* @Return array multiple (format compatible avec les resultats issus des fonctions de recherche) contenant les infos du titre � modifier (id, titre, ann�e)
*/

function TitreInfosID($idTitre)
{
	global $mysql_data;
	
	if( is_null($idTitre) === true || 
	strlen(trim($idTitre)) == 0) return false;
	//requete sur l'id du titre pour r�cup�rer ses informations
	$strQuery = "SELECT * FROM ".$mysql_data['tables']['titres']." WHERE idTitre='".$idTitre."'";
	$hResult = mysql_query($strQuery);
	if(!$hResult) return false;
	//formatage du retour sur le m�me modele que les fonctions de recherche
	$retour = mysql_fetch_array($hResult);
	$titreInfos = array();
	array_push($titreInfos, $retour);
	return $titreInfos;
}

/**
* 
* Fonction de suppression d'un titre ET de tous les couples associ�s
* 
* @Param $idTitre ID du titre � supprimer
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