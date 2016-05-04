<?php

/**
 * Biblioth�que de fonctions de couplage Titre / Interprete
 *
 * @author Damien Mathieu
 */

 /**
 * Fonction de couplage titre / Interpr�te par ID
 *
 * @param $idTitre Id du titre
 * @param $idInterprete Id de l'interprete
 * @param $Original bool 0 pour interprete non original par d�faut - 1 pour interprete original
 * @return Id du couple ins�r� sinon false
 */

function CoupleAjouterID($idTitre, $idInterprete, $Original=0)
{
	global $mysql_data;
	
	if( is_null($idTitre) === true || 
	$idTitre == 0) return false;
	
	if( is_null($idInterprete) === true || 
	$idInterprete == 0) return false;
	// liste des couples existants sur le titre demand�
	$origTitreExist = RechercheLireCorrespondancesID($idTitre, 0, 0);
	// marqueur de pr�sence d'un couple original
	$origVerifExist = 0;
	if ($origTitreExist != false) 
	// pour les couples concernant le titre demand�
	{
		// on verifie si l'un comporte le marqueur d'interprete original
		foreach ($origTitreExist as $arVerifExist)
		// si oui, on passe le marqueur � 1
			{$origVerifExist = $arVerifExist['interpreteOriginal'];}
	}
	// l'insertion ne se fait donc que si le couple exact n'existe pas encore et que
	//		- soit le titre ne comporte pas d�j� d'interprete original
	//		- soit l'interprete n'est pas l'original
	if ($Original == 1 AND $origVerifExist == 1)
	{
		return false;
	}
	else
	{
		$strQuery = "INSERT INTO " . $mysql_data['tables']['couples'] . " (idInterprete, idTitre, interpreteOriginal)
			VALUES ('".$idInterprete."', '".$idTitre."', '".$Original."')";
			$hResult = mysql_query($strQuery);
			if( !$hResult) return false;
		return mysql_insert_id();
	}
}
?>