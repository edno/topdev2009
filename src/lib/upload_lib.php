<?php
/**
 * Module de chargement de fichiers
 * Bibliothèque de fonctions
 *
 * @author Grégory Heitz
 */

require_once '.\lib\common_lib.php';
require_once '.\lib\search_lib.php';
require_once '.\lib\titre_lib.php';
require_once '.\lib\interprete_lib.php';
require_once '.\lib\couple_lib.php';

define('TMP_DIR','.\tmp');

/**
 * Fonction de chargement multiple de fichiers
 *
 * @param strUser pseudo de l'utilisateur, si vide alors Erreur 
 * @param arFilesUpload tableau contenant les informations des fichiers uploadés (item de $_FILES)
 * @return tableau contenant le statut de chargement de chaque fichier
 */
function UploadChargerMultiFichiers($arFilesUpload, $strUser)
{
	static $nbFiles;

	if( is_null($strUser) === true || 
	strlen(trim($strUser)) == 0) return false;

	// comptage du nombre de fichiers
	$nbFiles = 0;
	foreach($arFilesUpload['name'] as $item) $nbFiles += empty($item) ? 0 : 1;

	$ret = array();
	for( $n = 0 ; $n < $nbFiles ; $n++)
	{
		// en cas d'erreur lors de l'upload, on passe au fichier suivant
		if( $arFilesUpload['error'][$n] != 0) continue;
		// extraction des informations de chaque fichier
		$arFile = array( 'name'     => $arFilesUpload['name'][$n],
						 'tmp_name' => $arFilesUpload['tmp_name'][$n],
						 'size'     => $arFilesUpload['size'][$n]
					   );
		// chargement de chaque fichier
		ereg('^(.*)\.([A-Za-z0-9]{1,3})$', $arFile['name'], $regs);
		$ret[$n]['nom'] = $regs[1];
		$ret[$n]['ext'] = $regs[2];
		$ret[$n]['status'] = UploadChargerFichier($arFile, $strUser);
	}
	
	return $ret;
}

/**
 * Fonction de chargement unitaire de fichier
 *
 * @param arFileUpload tableau contenant les informations du fichier uploadé
 * @return boolean True si le chargement s'est bien passé, False sinon
 */
function UploadChargerFichier($arFileUpload, $strUser)
{
	if( is_null($strUser) === true || 
	strlen(trim($strUser)) == 0) return false;

	// enregistrement du fichier en repertoire temporaire et retourne le status correspondant
	return move_uploaded_file($arFileUpload['tmp_name'], TMP_DIR.'\\'.$arFileUpload['name']);
}

/**
 * Fonction de listage des fichiers
 *
 * @param strUser pseudo de l'utilisateur, si vide ou inexisitant en base alors erreur (false)
 * @param strLevel profil de l'utilisateur (admin | user), si 'admin' alors niveau gestionnaire sinon niveau utilisateur
 * @return tableau de résultats si le traitement s'est bien passé, False sinon
 */
function UploadListerFichiers($strUser, $strLevel=null)
{
	global $mysql_data;
	static $idUser;

	// traitement des utilisateurs
	if ( is_null($strUser) || 
		 strlen(trim($strUser)) == 0) return false;
	
	$idUser = UploadChercherProprietaire($strUser);
	if( !$idUser) return false;
	
	// recherche des fichiers
	$sqlRequete = "SELECT P.pseudoProprietaire, F.nomFichier, C.titreTitre, C.anneeTitre, C.prenomInterprete, C.nomInterprete, C.interpreteOriginal, F.idProprietaire, F.dateAjoutFichier"
				 ." FROM ".$mysql_data['tables']['fichiers']	 ." F,"
						  .$mysql_data['vues']['couplage'] 		 ." C,"
						  .$mysql_data['tables']['proprietaires']." P "
				 ." WHERE F.idCouple = C.idCouple"
				 ." AND F.idProprietaire = P.idProprietaire";
	// si utilisateur alors filtre sur utilisateur
	$sqlRequete .= $strLevel != 'admin' ? " AND F.idProprietaire=".$idUser : null;
	$sqlRequete .= " ORDER BY F.idProprietaire ASC, F.dateAjoutFichier DESC";
	
	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	
	// generation des résultats
	$ret = array();
	while($row = mysql_fetch_assoc($hResult))
	{	
		if(!@is_array($ret[$row['pseudoProprietaire']]['fichiers'])) $ret[$row['pseudoProprietaire']]['fichiers'] = array();
		array_push($ret[$row['pseudoProprietaire']]['fichiers'], array( 'nom'        => $row['nomFichier'],
																		'titre'      => array( 'titre'    => $row['titreTitre'],
																						       'annee'    => $row['anneeTitre']),
																		'interprete' => array( 'prenom'   => $row['prenomInterprete'],
																							   'nom'      => $row['nomInterprete'],
																							   'original' => $row['interpreteOriginal'])
																	   )
				   );
	}

	mysql_free_result($hResult);
	
	return $ret;
}

/**
 * Fonction déclaration en bdd des fichiers
 *
 * @param arFichiers tableau des fichiers à déclarer
 * @param strUser pseudo de l'utilisateur, si vide alors Erreur
 * @return tableau contenant le statut de déclaration de chaque fichier, False si le proprietaire n'existe pas
 */
function UploadDeclarerFichiers($arFichiers, $strUser)
{
	global $mysql_data, $cfgUploadFolder;
	static $arCouple, $idUser, $idCouple;
	
	// recherche de l'id du proprietaire
	$idUser = UploadChercherProprietaire($strUser);
	if( !$idUser) return false;
	
	$ret = array();
	// enregistrement de chaque fichier en base
	foreach( $arFichiers as $arFichier)
	{
		$idTitre = $arFichier['idTitre'];
		$idInterprete = $arFichier['idInterprete'];
		// si idTitre et idInterprete absent on creer les entités
		if($arFichier['idTitre'] == 0 || $arFichier['idInterprete'] == 0)	
		{		
			// creation d'un titre
			if( $idTitre == 0) $idTitre = TitreAjouter($arFichier['titre']);
			// traitement des erreurs
			if( !$idTitre)
			{
				array_push($ret, array( 'fichier' => $arFichier['fichier'], 'status' => false));
				continue;
			}
			
			// creation d'un interprete
			if( $idInterprete == 0) $idInterprete = InterpreteAjouter($arFichier['nom'], $arFichier['prenom']);
			// traitement des erreurs
			if( !$idInterprete) 
			{
				array_push($ret, array( 'fichier' => $arFichier['fichier'], 'status' => false));
				continue;
			}
		}
		
		// recherche de couple existant
		$arCouple = RechercheLireCorrespondancesID($idTitre, $idInterprete);
		if( !$arCouple)
		{
			$idCouple = CoupleAjouterID($idTitre, $idInterprete);
			// traitement des erreurs
			if( !$idCouple)
			{
				array_push($ret, array( 'fichier' => $arFichier['fichier'], 'status' => false));
				continue;
			}			
		}
		else $idCouple = $arCouple[0]['idCouplage'];

		// enregistrement en base des fichiers (table "fichiers")
		$sqlRequete = "INSERT INTO ".$mysql_data['tables']['fichiers']
					 ."      (    nomFichier         ,    idCouple   ,    idProprietaire)"
					 ." VALUE('".$arFichier['fichier']."',".$idCouple.",".$idUser.")";
		$bStatus = mysql_query($sqlRequete);
		//  si la declaration est correcte, deplacement du fichier dans le repertoire de stockage
		if( $bStatus) $bStatus = @rename(TMP_DIR.'\\'.$arFichier['fichier'], $cfgUploadFolder.'\\'.$arFichier['fichier']);
		// generation du tableau de résultats
		array_push($ret, array( 'fichier' => $arFichier['fichier'], 'status' => $bStatus));
	}
	
	return $ret;
}

/**
 * Fonction de recherche de proprietaire
 *
 * @param strUser pseudo de l'utilisateur, si vide alors pas de filtrage sur les utilisateurs
 * @return identifiant du proprietaire en base, False sinon
 */
function UploadChercherProprietaire($strUser)
{
	global $mysql_data;

	if( is_null($strUser) === true || 
		strlen(trim($strUser)) == 0) return false;
	
	// recherche de proprietaire par son pseudo
	$sqlRequete = "SELECT idProprietaire"
				 ." FROM ".$mysql_data['tables']['proprietaires']
				 ." WHERE pseudoProprietaire='".$strUser."'";
	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	
	// vérification de l'unicité
	if( mysql_num_rows($hResult)<>1) return false;
	$row = mysql_fetch_row($hResult);
	
	return $row[0];
}

/**
 * Fonction de suppression de fichier local
 *
 * @param strFichier nom du fichier a supprimer, si vide alors erreur (false)
 * @return True si suppression effectuée, False sinon
 */
function UploadSupprimerFichierLocal($strFichier)
{
	if( is_null($strFichier) === true || 
		strlen(trim($strFichier)) == 0) return false;

	$strTarget = TMP_DIR.'\\'.$strFichier;
		
	// suppression du fichier s'il existe
	if(file_exists($strTarget))
		return unlink($strTarget);
	else return false;
}

/**
 * Fonction de suppression de fichier en base
 *
 * @param strFichier nom du fichier a supprimer, si vide alors erreur (false)
 * @return True si suppression effectuée, False sinon
 */
function UploadSupprimerFichierBase($strFichier)
{
	global $mysql_data;

	if( is_null($strFichier) === true || 
		strlen(trim($strFichier)) == 0) return false;

	$strTarget = $uploadFolder.'\\'.$strFichier;
		
	$sqlRequet = "DELETE FROM ".$mysql_data['tables']['fichiers']." WHERE nomFichier='".$strFichier."'";
	return mysql_query($sqlRequete);
}
?>