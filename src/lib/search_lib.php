<?php
/**
 * Moteur de recherche
 * Bibliothèque de fonctions
 *
 * @author Grégory Heitz
 */

require_once '.\lib\common_lib.php';

// définition des constantes pour les listes de caractères diacritiques
define('LISTE_DIACRITIQUES', 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ'); // ne pas mettre les crochets de liste
define('LISTE_A', '[AÀÁÂÃÄÅ]');
define('LISTE_C', '[CÇ]');
define('LISTE_E', '[EÈÉÊË]');
define('LISTE_I', '[IÌÍÎÏ]');
define('LISTE_O', '[OÒÓÔÕÖ]');
define('LISTE_U', '[UÙÚÛÜ]');
define('LISTE_Y', '[YÝ]');

/**
 * Fonction de création d'une table temporaire de couplage
 * REMPLACEMENT DE LA VUE COUPLAGE INITIALEMENT PREVUE
 *
 * return True si la création s'est bien passée, False sinon
 */
function RechercheCreerTableTemporaireCouplage()
{
	global $mysql_data;

	/** Creation de la table temporaire  **/
	// suppression de la table temporaire si elle existe déjà
	$sqlRequete = "DROP TEMPORARY TABLE IF EXISTS ".$mysql_data['vues']['couplage'];
	if( !mysql_query($sqlRequete)) return false;
	
	// création de la table temporaire de stockage des résultats de recherche de chaque mot
	$sqlRequete = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$mysql_data['vues']['couplage']." (
					`idCouple` INT UNSIGNED NOT NULL,	
					`idTitre` INT UNSIGNED NOT NULL,
					`titreTitre` VARCHAR(250) NOT NULL,
					`anneeTitre` YEAR NULL DEFAULT NULL,
					`idInterprete` INT UNSIGNED NOT NULL,
					`prenomInterprete` VARCHAR(100) NOT NULL,					
					`nomInterprete` VARCHAR(100) NOT NULL,
					`interpreteOriginal` BOOLEAN NOT NULL)
					ENGINE = InnoDB;";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin table temporaire **/	

	/** Remplissage de la table temporaire **/
	$sqlRequete = "INSERT INTO ".$mysql_data['vues']['couplage']
				 ." (SELECT C.idCouple, C.idTitre, T.titreTitre, T.anneeTitre, C.idInterprete, I.prenomInterprete, I.nomInterprete, C.interpreteOriginal"
				 ." FROM ".$mysql_data['tables']['couples']." C, ".$mysql_data['tables']['titres']." T, ".$mysql_data['tables']['interpretes']." I"
				 ." WHERE C.idTitre = T.idTitre AND C.idInterprete = I.idInterprete);";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin remplissage de la table **/

	return true;
}

/**
 * Fonction de recherche d'interprete original pour un titre
 *
 * @param strPattern chaine du titre à rechercher
 * @param bOriginal si True retourne uniquement l'interprete original du titre
 * @return un tableau de données [idCouple,idTitre,titreTitre,anneeTitre,idInterprete,prenomInterprete,nomInterprete,interpreteOriginal,total] si la recherche s'est bien passée, False sinon
 */
function RechercheLireTitreInterpretes($strPattern, $bOriginal=false)
{
	if( is_null($strPattern) === true || 
		strlen(trim($strPattern)) == 0) return false;
	
	// recherche du titre
	$arTitres = RechercheLireTitres($strPattern, 0, false, true);
	if( !$arTitres) return false;
	
	// recherche des couples
	$arCouplages = array();
	foreach( $arTitres as $arTitre)
	{
		$arCouples = RechercheLireCorrespondancesID($arTitre['idTitre']);
		foreach( $arCouples as $arCouple) array_push($arCouplages, $arCouple);
	}
	if( !$arCouplages) return false;
	
	// recherche des interpretes originaux
	$ret = array();
	foreach( $arCouplages as $arCouple)
	{
		// traitement des interpretes originaux
		if( $arCouple['interpreteOriginal'] && $bOriginal) array_push($ret, $arCouple);
		elseif( !$bOriginal) array_push($ret, $arCouple);
	}
	
	if(empty($ret)) return false;
	
	return $ret;
}

/**
 * Fonction de recherche des titres pour un interprete
 *
 * @param strPattern chaine de l'interprete à rechercher
 * @param bOriginal si True retourne uniquement les titres où l'artiste est interprete original
 * @return un tableau de données [idCouple,idTitre,titreTitre,anneeTitre,idInterprete,prenomInterprete,nomInterprete,interpreteOriginal,total] si la recherche s'est bien passée, False sinon
 */
function RechercheLireInterpreteTitres($strPattern, $bOriginal=false)
{
	if( is_null($strPattern) === true || 
		strlen(trim($strPattern)) == 0) return false;
	
	// recherche de l'interprete
	$arInterpretes = RechercheLireInterpretes($strPattern, 0, false, true);
	if( !$arInterpretes) return false;
	
	// recherche des couples
	$arCouplages = array();
	foreach( $arInterpretes as $arInterprete)
	{
		$arCouples = RechercheLireCorrespondancesID(0, $arInterprete['idInterprete']);
		foreach( $arCouples as $arCouple) array_push($arCouplages, $arCouple);
	}
	if( !$arCouplages) return false;
	
	// recherche des titres
	$ret = array();
	foreach( $arCouplages as $arCouple)
	{
		// traitement des interpretes originaux
		if( $arCouple['interpreteOriginal'] && $bOriginal) array_push($ret, $arCouple);
		elseif( !$bOriginal) array_push($ret, $arCouple);
	}
	
	if(empty($ret)) return false;
	
	return $ret;
}

/**
 * Fonction de recherche de correspondances par identifiant de titre ou de
 *
 * @param idTitre ID du titre pour le couple cherché
 * @param idInterprete ID du titre pour le couple cherché
 * @param nTop nombre de resultats attendu, si 0 alors tous les résultats seront retournés
 * @return un tableau de données [idCouple,idTitre,titreTitre,anneeTitre,idInterprete,prenomInterprete,nomInterprete,interpreteOriginal,total] si la recherche s'est bien passée, False sinon
 */
function RechercheLireCorrespondancesID($idTitre=0, $idInterprete=0, $nTop=0)
{
	global $mysql_data;

	if( !RechercheCreerTableTemporaireCouplage()) return false;
	
	/** Creation d'une table temporaire pour les couples trouvés **/
	$strTableResultatTemp = 'resultat_temp';
	// suppression de la table temporaire si elle existe déjà
	$sqlRequete = "DROP TEMPORARY TABLE IF EXISTS ".$strTableResultatTemp;
	if( !mysql_query($sqlRequete)) return false;
	// création de la table temporaire de stockage des résultats de recherche de chaque mot
	$sqlRequete = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$strTableResultatTemp." (
					`idCouplage` INT UNSIGNED NOT NULL,	
					`idTitre` INT UNSIGNED NOT NULL,
					`titreTitre` VARCHAR(250) NOT NULL,
					`anneeTitre` YEAR NULL DEFAULT NULL,
					`idInterprete` INT UNSIGNED NOT NULL,
					`prenomInterprete` VARCHAR(100) NOT NULL,					
					`nomInterprete` VARCHAR(100) NOT NULL,
					`interpreteOriginal` BOOLEAN NOT NULL)
					ENGINE = InnoDB;";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin table temporaire **/	

	/** Recherche de chaque ID dans la vue de couplage et stockage en table temporaire **/
	// génération de l'expression reguliere de recherche
	$sqlRequete = "INSERT INTO ".$strTableResultatTemp
				 ." (SELECT * FROM ".$mysql_data['vues']['couplage']
				 ." WHERE ";
	$sqlRequete .= $idTitre > 0                      ? "idTitre=".$idTitre : null;
	$sqlRequete .= $idTitre > 0 && $idInterprete > 0 ? " AND " : null;
	$sqlRequete .= $idInterprete > 0                 ? "idInterprete=".$idInterprete : null;
	$sqlRequete .= ")";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin de la recherche **/

	/** Generation des resultats **/
	$sqlRequete = "SELECT *, COUNT(idTitre) total FROM ".$strTableResultatTemp." AS QUERY GROUP BY idTitre, idInterprete ORDER BY total DESC";
	// si un top est demandé on limite, sinon pas de limitation
	$sqlRequete .= $nTop > 0 ? " LIMIT 0,".$nTop : null;

	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	$arRetour= array();
	while( $row = mysql_fetch_assoc($hResult)) array_push($arRetour, $row);
	/** Fin generation des resultats **/
	
	mysql_free_result($hResult);
		
	return $arRetour;
}


/**
 * Fonction de recherche de correspondances par chaine de référence
 *
 * @param strPattern chaine à rechercher dans les couples (titre, nom, prenom)
 * @param nTop nombre de resultats attendu, si 0 alors tous les résultats seront retournés
 * @return un tableau de données [idCouple,idTitre,titreTitre,anneeTitre,idInterprete,prenomInterprete,nomInterprete,interpreteOriginal,total] si la recherche s'est bien passée, False sinon
 */
function RechercheLireCorrespondances($strPattern=null, $nTop=0)
{
	global $mysql_data;
	
	if( !RechercheCreerTableTemporaireCouplage()) return false;
	
	// génération de l'expression reguliere de recherche
	$arRequete = RecherchePreparerRequete($strPattern);
	if( !$arRequete) return false;	

	/** Creation d'une table temporaire pour les couples trouvés **/
	$strTableResultatTemp = 'resultat_temp';
	// suppression de la table temporaire si elle existe déjà
	$sqlRequete = "DROP TEMPORARY TABLE IF EXISTS ".$strTableResultatTemp;
	if( !mysql_query($sqlRequete)) return false;
	// création de la table temporaire de stockage des résultats de recherche de chaque mot
	$sqlRequete = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$strTableResultatTemp." (
					`idCouplage` INT UNSIGNED NOT NULL,	
					`idTitre` INT UNSIGNED NOT NULL,
					`titreTitre` VARCHAR(250) NOT NULL,
					`anneeTitre` YEAR NULL DEFAULT NULL,
					`idInterprete` INT UNSIGNED NOT NULL,
					`prenomInterprete` VARCHAR(100) NOT NULL,					
					`nomInterprete` VARCHAR(100) NOT NULL,
					`interpreteOriginal` BOOLEAN NOT NULL)
					ENGINE = InnoDB;";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin table temporaire **/	
	
	/** Recherche de chaque mot dans la vue de couplage et stockage en table temporaire **/
	// recherche de chaque mot dans la table temporaire de recherche
	foreach( $arRequete as $strItem)
	{
		// chaque resultat est ajouté à la table temporaire
		$sqlRequete = "INSERT INTO ".$strTableResultatTemp
					 ." (SELECT * FROM ".$mysql_data['vues']['couplage']
					 ." WHERE UPPER(titreTitre) REGEXP '".$strItem."' OR UPPER(CONCAT_WS(' ', prenomInterprete, nomInterprete)) REGEXP '".$strItem."');";
		if( !mysql_query($sqlRequete)) return false;
	}	
	/** Fin de la recherche **/
	
	/** Generation des resultats **/
	$sqlRequete = "SELECT *, COUNT(idTitre) total FROM ".$strTableResultatTemp." AS QUERY GROUP BY idTitre, idInterprete ORDER BY total DESC";
	// si un top est demandé on limite, sinon pas de limitation
	$sqlRequete .= $nTop > 0 ? " LIMIT 0,".$nTop : null;

	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	$arRetour= array();
	while( $row = mysql_fetch_assoc($hResult)) array_push($arRetour, $row);
	/** Fin generation des resultats **/
	
	mysql_free_result($hResult);
		
	return $arRetour;
}

/**
 * Fonction de recherche d'interpretes
 *
 * @param strPattern chaine à rechercher dans les interpretes, si vide alors tous les interpretes seront retournés
 * @param nTop nombre de resultats attendu, si 0 alors tous les résultats seront retournés
 * @param bFlagAlphaOrder flag pour tri par ordre alphabetique, si false alors tri par total (si strPattern non vide) ou date d'ajout
 * @param bExactSearch flag pour la recherche exacte de l'interprete, si false alors recherche fulltext 
 * @param bIntuitive flag pour la saisie partielle de mot, si false alors recherche par mot complet 
 * @return un tableau d'interpretes [idInterprete,prenomInterprete,nomInterprete,total,dateAjoutInterprete] si la recherche s'est bien passée, False sinon
 */
function RechercheLireInterpretes($strPattern, $nTop=0, $bFlagAlphaOrder=false, $bExactSearch=false, $bIntuitive=false)
{
	global $mysql_data;
	
	// nettoyage de la recherche pour éviter les champs "blancs"
	$strPattern = trim($strPattern);

	// génération de l'expression reguliere de recherche
	if( $bExactSearch) // si recherche exacte
	{
		$arRequete = array(strtoupper($strPattern));
	}
	else
	{
		$arRequete = strlen($strPattern) ? RecherchePreparerRequete($strPattern, $bIntuitive) : array('.*');
		if( !$arRequete) return false;
	}
	
	/** Creation d'une table temporaire pour les interpretes trouvés **/
	$strTableResultatTemp = 'resultat_temp';
	// suppression de la table temporaire si elle existe déjà
	$sqlRequete = "DROP TEMPORARY TABLE IF EXISTS ".$strTableResultatTemp;
	if( !mysql_query($sqlRequete)) return false;
	// création de la table temporaire de stockage des résultats de recherche de chaque mot
	$sqlRequete = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$strTableResultatTemp." (
					`idInterprete` INT UNSIGNED NOT NULL,
					`prenomInterprete` VARCHAR(100) NOT NULL,					
					`nomInterprete` VARCHAR(100) NOT NULL,
					`dateAjoutInterprete` TIMESTAMP NOT NULL)
					ENGINE = InnoDB;";
	if( !mysql_query($sqlRequete)) return false;
	/** Fin table temporaire **/

	/** Recherche de chaque mot dans la table des interpretes **/
	// recherche de chaque mot dans la table des titres
	foreach( $arRequete as $strItem)
	{
		$strItem = $bExactSearch ? '^'.$strItem.'$' : $strItem;
		// chaque resultat est ajouté à la table temporaire
		$sqlRequete = "INSERT INTO ".$strTableResultatTemp
					 ." (SELECT idInterprete, prenomInterprete, nomInterprete, dateAjoutInterprete"
					 ." FROM ".$mysql_data['tables']['interpretes']
					 ." WHERE UPPER(CONCAT_WS(' ', prenomInterprete, nomInterprete)) REGEXP '".$strItem."');";				 
		if( !mysql_query($sqlRequete)) return false;
	}
	/** Fin de la recherche **/
	
	/** Generation des resultats **/
	$sqlRequete = "SELECT idInterprete, prenomInterprete, nomInterprete, COUNT(idInterprete) total, dateAjoutInterprete FROM ".$strTableResultatTemp." AS QUERY GROUP BY idInterprete";
	// si tri alphabetique demande
	if($bFlagAlphaOrder) $sqlRequete .= " ORDER BY prenomInterprete, nomInterprete DESC";
	// sinon si on a un critere de recherche alors on classe par pertinence, sinon par date de creation
	else $sqlRequete .= strlen($strPattern) ? " ORDER BY total DESC" : " ORDER BY dateAjoutInterprete DESC";
	// si un top est demandé on limite, sinon pas de limitation
	$sqlRequete .= $nTop > 0 ? " LIMIT 0,".$nTop : null;

	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	$arRetour= array();
	while( $row = mysql_fetch_assoc($hResult)) array_push($arRetour, $row);
	/** Fin generation des resultats **/
	
	mysql_free_result($hResult);
		
	return $arRetour;
}

/**
 * Fonction de recherche de titre
 *
 * @param strPattern chaine à rechercher dans les titres, si vide alors tous les titres seront retournés
 * @param nTop nombre de resultats attendu, si 0 alors tous les résultats seront retournés
 * @param bFlagAlphaOrder flag pour tri par ordre alphabetique, si false alors tri par total (si strPattern non vide) ou date d'ajout
 * @param bExactSearch flag pour la recherche exacte du titre, si false alors recherche fulltext
 * @param bIntuitive flag pour la saisie partielle de mot, si false alors recherche par mot complet
 * @return un tableau des résultats [idTitre,titreTitre,anneeTitre,totale,dateAjoutTitre] si la recherche s'est bien passée, False sinon
 */
function RechercheLireTitres($strPattern, $nTop=0, $bFlagAlphaOrder=false, $bExactSearch=false, $bIntuitive=false)
{
	global $mysql_data;
	
	// nettoyage de la recherche pour éviter les champs "blancs"
	$strPattern = trim($strPattern);

	// génération de l'expression reguliere de recherche
	if( $bExactSearch) // si recherche exacte
	{
		$arRequete = array(strtoupper($strPattern));
	}
	else // sinon recherche fulltext de tous les mots
	{
		$arRequete = strlen($strPattern) ? RecherchePreparerRequete($strPattern, $bIntuitive) : array('.*');
		if( !$arRequete) return false;
	}	
	
	/** Creation d'une table temporaire pour les titres trouvés **/
	$strTableResultatTemp = 'resultat_temp';
	// suppression de la table temporaire si elle existe déjà
	$sqlRequete = "DROP TEMPORARY TABLE IF EXISTS ".$strTableResultatTemp;
	if( !mysql_query($sqlRequete)) return false;
	// création de la table temporaire de stockage des résultats de recherche de chaque mot
	$sqlRequete = "CREATE TEMPORARY TABLE IF NOT EXISTS ".$strTableResultatTemp." (
					`idTitre` INT UNSIGNED NOT NULL,
					`titreTitre` VARCHAR(250) NOT NULL,
					`anneeTitre` YEAR NULL DEFAULT NULL,
					`dateAjoutTitre` TIMESTAMP NOT NULL)
					ENGINE = InnoDB;";		
	if( !mysql_query($sqlRequete)) return false;
	/** Fin table temporaire **/

	/** Recherche de chaque mot dans la table des titres **/
	// recherche de chaque mot dans la table des titres
	foreach( $arRequete as $strItem)
	{
		$strItem = $bExactSearch ? '^'.$strItem.'$' : $strItem;
		// chaque resultat est ajouté à la table temporaire
		$sqlRequete = "INSERT INTO ".$strTableResultatTemp
					 ." (SELECT * "
					 ." FROM ".$mysql_data['tables']['titres']
					 ." WHERE UPPER(titreTitre) REGEXP '".$strItem."');";
		if( !mysql_query($sqlRequete)) return false;
	}
	/** Fin de la recherche **/
	
	/** Generation des resultats **/
	$sqlRequete = "SELECT idTitre, titreTitre, anneeTitre, COUNT(idTitre) total, dateAjoutTitre FROM ".$strTableResultatTemp." AS QUERY GROUP BY idTitre";
	// si tri alphabetique demande
	if($bFlagAlphaOrder) $sqlRequete .= " ORDER BY titreTitre DESC";
	// sinon si on a un critere de recherche alors on classe par pertinence, sinon par date de creation
	else $sqlRequete .= strlen($strPattern) ? " ORDER BY total DESC" : " ORDER BY dateAjoutTitre DESC";
	// si un top est demandé on limite, sinon pas de limitation
	$sqlRequete .= $nTop > 0 ? " LIMIT 0,".$nTop : null;

	$hResult = mysql_query($sqlRequete);
	if( !$hResult) return false;
	$arRetour= array();
	while( $row = mysql_fetch_assoc($hResult)) array_push($arRetour, $row);
	/** Fin generation des resultats **/
	
	mysql_free_result($hResult);
		
	return $arRetour;
}

/**
 * Fonction de preparation requêtes de recherche
 *
 * @param strPattern Chaîne de caractères à chercher
 * @param bIntuitive flag pour la saisie partielle de mot, si false alors isolation des mots complets
 * @return un tableau de regexp si la tranformation s'est bien passée, False sinon
 */
function RecherchePreparerRequete($strPattern, $bIntuitive=false)
{
	if( is_null($strPattern) === true || 
		strlen(trim($strPattern)) == 0) return false;
		
	$strRequete = RechercheSeparerMots($strPattern);
	if( !$strRequete) return false;
	$strRequete = RechercheExclureMots($strRequete);
	if( !$strRequete) return false;
	$arRequete  = RechercheGenererExpressions($strRequete, $bIntuitive);
	if( !$arRequete) return false;
	
	return $arRequete;
}

/**
 * Fonction de génération des expressions régulières de recherche des mots de la requête
 *
 * @param strRequete Chaîne de caractères à traiter 
 * @param bIntuitive flag pour la saisie partielle de mot, si false alors isolation des mots complets 
 * @return un tableau de regexp si la tranformation s'est bien passée, False sinon
 */
function RechercheGenererExpressions($strRequete, $bIntuitive=false)
{
	global $cfgSearchPluriel, $cfgSearchDiacritique, $cfgSearchExact; // paramètres de configuration (config.php)	

	if( is_null($strRequete) === true || 
		strlen(trim($strRequete)) == 0) return false;
	
	$arData = explode(' ', $strRequete);
	
	$arRetour = array();
	foreach( $arData as $strMot)
	{
		// passage en casse majuscule des mots
		$strMot = strtoupper($strMot);
		
		// traitement du pluriel des mots (uniquement le cas du 'S')
		if( !$cfgSearchPluriel) $strMot = RechercheGenererRegExpPluriel($strMot);
		
		// traitement des différentes terminaisons de mots possibles
		if( !$cfgSearchExact) $strMot = RechercherGenererRegExpFin($strMot);
		
		// traitement des caractères diacritiques (accents et cédille)
		if( !$cfgSearchDiacritique) $strMot = RechercherGenererRegExpDiacritique($strMot);
		
		// isolement du mot dans la chaine complète (le mot ne doit pas être précédé ou suivi de caractères alphanumériques
		if( !$bIntuitive) $strMot = '(^|[^A-Za-z0-9'.LISTE_DIACRITIQUES.'])'.$strMot.'([^A-Za-z0-9'.LISTE_DIACRITIQUES.']|$)';
		
		array_push($arRetour, $strMot);
	}
	return $arRetour;
}

/**
 * Fonction de transformation de mot pour regexp insensibilité aux caractères diacritiques (accents, cédille...)
 *
 * @param strMot mot a traiter, si vide alors erreur (False)
 * @return chaine correspondant au mot transformé avec un regexp diacritique si le traitement s'est bien passé, False sinon
 */
function RechercherGenererRegExpDiacritique($strMot)
{
	if( is_null($strMot) === true || 
		strlen(trim($strMot)) == 0) return false;

	// remplacement de chaque variation des caractères ACEIOUY par le groupe regexp de variations possibles
	$strMot = eregi_replace(LISTE_A, LISTE_A, $strMot);
	$strMot = eregi_replace(LISTE_C, LISTE_C, $strMot);
	$strMot = eregi_replace(LISTE_E, LISTE_E, $strMot);
	$strMot = eregi_replace(LISTE_I, LISTE_I, $strMot);
	$strMot = eregi_replace(LISTE_O, LISTE_O, $strMot);
	$strMot = eregi_replace(LISTE_U, LISTE_U, $strMot);
	$strMot = eregi_replace(LISTE_Y, LISTE_Y, $strMot);
	
	return $strMot;
}

/**
 * Fonction de transformation de mot pour regexp de différentes terminaisons
 *
 * @param strMot mot a traiter, si vide alors erreur (False)
 * @return chaine correspondant au mot transformé avec un regexp des terminaisons possibles si le traitement s'est bien passé, False sinon
 */
function RechercherGenererRegExpFin($strMot)
{
	if( is_null($strMot) === true || 
		strlen(trim($strMot)) == 0) return false;

	// récupération des fins similaires
	$arFins = RechercheListerFins();
	foreach( $arFins as $strFin)
	{
		// remplacement de chaque terminaison identifiée par le groupe regexp des terminaisons similaires
		$strMot = eregi_replace('['.$strFin.']$', '['.$strFin.']', $strMot);
	}
	return $strMot;
}

/**
 * Fonction de transformation de mot pour regexp de pluriel/singulier (avec ou sans 'S')
 *
 * @param strMot mot a traiter, si vide alors erreur (False)
 * @return chaine correspondant au mot transformé avec un regexp pluriel si le traitement s'est bien passé, False sinon
 */
function RechercheGenererRegExpPluriel($strMot){

	if( is_null($strMot) === true || 
		strlen(trim($strMot)) == 0) return false;

	// traitement s'il le mot se termine par un 'S'
	if( eregi('([A-Z]+)(S)$', $strMot))
	{
		// suppression du 'S' final s'il existe
		$strMot = substr($strMot, 0, -1);
	}
	// ajout du regexp 'S' facultatif
	$strMot = $strMot.'(S?)';
	
	return $strMot;
}

/**
 * Fonction de récupération du dictionnaire des caractère de séparation
 *
 * @return un tableau de caractère si la récupération s'est bien passée, False sinon
 */
function RechercheListerSeparateurs()
{
	global $cfgSearchSeparateurs; // paramètre de configuration (config.php)
	
	$arRetour = str_split($cfgSearchSeparateurs);
	array_walk($arRetour, "addregexslashes");
		
	return $arRetour;
}

/**
 * Fonction de récupération du dictionnaire des mots exclus
 *
 * @return un tableau de mots si la récupération s'est bien passée, False sinon
 */
function RechercheListerMotsExclus()
{
	global $cfgSearchDico; // paramètre de configuration (config.php)
	
	$fp = fopen($cfgSearchDico['blacklist'], 'r');
	if( $fp)
	{
		$arRetour = array();
		while( !feof($fp)) {
			$mot = fgets($fp);
			array_push($arRetour, trim($mot));
		}
		fclose($fp);
		return $arRetour;
	}
	return false;
}

/**
 * Fonction de récupération du dictionnaire des fins approchantes
 *
 * @return un tableau de mots si la récupération s'est bien passée, False sinon
 */
function RechercheListerFins()
{
	global $cfgSearchDico; // paramètre de configuration (config.php)
	
	$fp = fopen($cfgSearchDico['similitudes'], 'r');
	if( $fp)
	{
		$arRetour = array();
		while( !feof($fp)) {
			$mots = fgets($fp);
			array_push($arRetour, trim(strtoupper($mots)));
		}
		fclose($fp);
		return $arRetour;
	}
	return false;
}

/**
 * Fonction d'extraction des mots de la requête
 *
 * @param strRequete Chaîne de caractères à traiter
 * @return la requete traitée si l'extraction s'est bien passée, False sinon
 */
function RechercheSeparerMots($strRequete)
{	
	if( is_null($strRequete) === true || 
		strlen(trim($strRequete)) == 0) return false;

	$arSeparateurs = RechercheListerSeparateurs();
	if( $arSeparateurs === false) return false;
		
	$strPattern = implode($arSeparateurs);
	
	$strRetour = ereg_replace('['.$strPattern.']+', 
							  ' ', 
							  $strRequete);

	$strRetour = trim($strRetour);
	// si la chaine retournée est vide alors erreur
	if( strlen($strRetour) == 0) return false;
	
	return $strRetour;
}

/**
 * Fonction d'exclusion des mots à ne pas rechercher
 *
 * @param strRequete Chaîne de caractères à traiter
 * @return la requete traitée si l'extraction s'est bien passée, False sinon
 */
function RechercheExclureMots($strRequete)
{
	global $cfgSearchTailleMot; // paramètre de configuration (config.php)
	
	$searchTailleMot  = $cfgSearchTailleMot < 1 ? 1 : $cfgSearchTailleMot;
	
	if( is_null($strRequete) === true || 
		strlen(trim($strRequete)) == 0) return false;

	// generation du pattern REGEXP pour les mot exclus
	$arMotsExclus = RechercheListerMotsExclus();
	//if( $arMotsExclus === false) return false;
	$strPattern = implode('|', $arMotsExclus);
	$strPattern = '(^| )(('.$strPattern.')|([A-Za-z0-9]|['.LISTE_DIACRITIQUES.']){1,'.$searchTailleMot.'})( |$)';

	// suppression des mots exclus et de taille interdites
	// boucle tant qu'il existe des mots interdits (taille ou blacklist)
	$strRetour = '';
	while( true)
	{
		$strRetour = eregi_replace( $strPattern, ' ', $strRequete);
		// si la taille apres traitement est égale a la taille avant traitement alors on a terminé
		if($strRetour === $strRequete) break;
		$strRequete = $strRetour;
	}
	
	$strRetour = trim($strRetour);
	// si la chaine retournée est vide alors erreur
	if( strlen($strRetour) == 0) return false;
	
	return $strRetour;
}
?>