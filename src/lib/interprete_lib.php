<?php
/**
 * Gestion des interpretes
 * Bibliothèque de fonctions
 *
 * @author Damien Mathieu
 */


/**
* 
* Fonction d'ajout de l'interprete
* 
* @Param $addNom Nom de l'interprète à insérer
* @Param $addPrenom Prenom de l'interprète à insérer
* @Return id de l'interprete inséré si ok, id de l'interprète déjà en base sinon
*/

function InterpreteAjouter($addNom, $addPrenom=null)
{
	global $mysql_data;
	if( is_null($addNom) === true || 
	strlen(trim($addNom)) == 0) return false;
	
	$addFullName = $addPrenom . ' ' . $addNom;
	// controle de l'unicité du nom en base
	$InterpreteExiste = RechercheLireInterpretes($addFullName, 0, false, true);
	
	// si nom unique - insertion et renvoi de l'ID
	if(!$InterpreteExiste)
	{
		$strQuery = "INSERT INTO " . $mysql_data['tables']['interpretes'] . " (nomInterprete, prenomInterprete) 
					VALUES ('" . $addNom . "','" . $addPrenom . "')";
		$hResult = mysql_query($strQuery);
		if(!$hResult) return false;
		return mysql_insert_id();
	}
	else
	// sinon renvoi de l'ID deja existante 
	{
		return $InterpreteExiste[0]['idInterprete'];
	}
}

/**
* 
* Fonction de suppression d'interprete
* 
* @Param $idInterprete
* @Return true si la suppression s'est bien passée, false sinon
*/

function InterpreteSupprimer($idInterprete)
{
	global $mysql_data;
	if( is_null($idInterprete) === true || 
	strlen(trim($idInterprete)) == 0) return false;
	//controle de l'existence de couples associés à l'interprete
	$controleCouple = RechercheLireCorrespondancesID(0, $idInterprete);
	//s'il n'y en a pas, suppression de l'interprete
	if (!$controleCouple)
	{
		$strQuery = "DELETE FROM " . $mysql_data['tables']['interpretes'] . " WHERE idInterprete='" . $idInterprete . "'";
		$hResult = mysql_query($strQuery);
		if(!$hResult) return false;
		return true;
	}
	else
	{
	return false;
	}
}

/**
* 
* Fonction de modification d'interprete
* 
* @Param $idInterprete id de l'interprete à modifier
* @Param $modNom nouveau nom de l'interprete
* @Param $modPrenom nouveau prenom de l'interprete
* @Return true si la modification s'est bien passée, false sinon
*/

function InterpreteMaj($idInterprete, $modNom, $modPrenom)
{
	global $mysql_data;
	if( is_null($modNom) === true || 
	strlen(trim($modNom)) == 0) return false;
	//mise a jour des informations selon l'id
	$strQuery = "UPDATE " . $mysql_data['tables']['interpretes'] . "
					SET nomInterprete='".$modNom."', prenomInterprete='".$modPrenom."'
					WHERE idInterprete='" . $idInterprete . "'";
		$hResult = mysql_query($strQuery);
		if(!$hResult) return false;
		return true;
}
?>