<?php
/**
 * Module de chargement de fichiers
 * Affichage des données
 *
 * @author Grégory Heitz
 */

 require_once './lib/search_lib.php';
 
/**
 * Fonction d'affichage du formulaire d'upload des fichiers
 *
 */
function UploadFormulaireImport()
{	
	include './frm/upload_frm.php';
}

/**
 * Fonction d'affichage du formulaire d'éditions des fichiers uploadés
 * 
 * @param arFichiers tableau des fichiers uploadés
 * return True si pas d'erreur, False si arFichiers est vide 
 */
function UploadFormulaireEdition($arFichiers)
{	
	if( empty($arFichiers)) return false;

	$arPropositions = array();
	foreach( $arFichiers as $arFichier)
	{
		// en cas d'erreur au chargement du fichier, le fichier n'est pas traité
		if( !$arFichier['status']) continue;
		$arFichier['titres'] = RechercheLireTitres($arFichier['nom']);
		if(empty($arFichier['titres'])) $arFichier['titres']=array();
		$arFichier['interpretes'] = RechercheLireInterpretes($arFichier['nom']);
		if(empty($arFichier['interpretes'])) $arFichier['interpretes']=array();
		array_push($arPropositions, $arFichier);
	}
	include './frm/upload2_frm.php';
	
	return true;
}

/**
 * Fonction d'affichage de la liste des fichiers uploadés
 *
 * @param arFichiers tableau des fichiers uploadés déclarés en base de données
 */
function UploadAfficherFichiers($arFichiers)
{	
	// si le tableau est vide alors pas de fichiers
	if( empty($arFichiers)) {
		echo '<div class="box-info">Aucun fichier disponible.</div>';
		return;
	}

	// affichage de chaque proprietaire
	foreach( $arFichiers as $strPseudo=>$arProprietaire)
	{
		echo '<fieldset>';
		echo '<legend> Fichiers de ', $strPseudo, '</legend>';
		echo '<table width="100%">'
					,'<thead><tr>'
						,'<th width="300px">Fichier</th>'
						,'<th width="200px">Titre</th>'
						,'<th>Ann&eacute;e</th>'
						,'<th>Interpr&egrave;te (pr&eacute;nom)</th>'
						,'<th>Interpr&egrave;te (nom)</th>'						
						,'<th>Interpr&egrave;te Original</th>'
					,'</tr></thead>';
		// affichage des fichiers du proprietaire
		foreach( $arProprietaire['fichiers'] as $arFichier)
		{
			// si pas d'année renseigné on affiche '----' sinon on affiche l'année
			$strAnnee = intval($arFichier['titre']['annee']) == 0 ? '----' : $arFichier['titre']['annee'];
			// si interprete original on affiche 'oui' sinon on affiche 'non'
			$strOriginal = intval($arFichier['interprete']['original']) == 0 ? 'non' : 'oui';
			
			echo '<tbody><tr class="ligne" onmouseover="this.className=\'ligne_over\';" onmouseout="this.className=\'ligne\';">'
						,'<td>', $arFichier['nom'], '</td>'
						,'<td>', $arFichier['titre']['titre'], '</td>'
						,'<td>', $strAnnee, '</td>'
						,'<td>', $arFichier['interprete']['prenom'], '</td>'
						,'<td>', $arFichier['interprete']['nom'], '</td>'						
						,'<td>', $strOriginal, '</td>'
					,'</tr></tbody>';
		}
		echo '</table>';
		echo '</fieldset>';	
	}
}

/**
 * Fonction d'affichage du statut d'upload des fichiers
 *
 * @param strFichier fichier en erreur, si vide pas de traitement
 */
function UploadAffichierEtatChargement($arFichiers)
{	
	foreach( $arFichiers as $arFichier)
	{
		if( $arFichier['status'] === true) echo '<div class="box-succes">Le fichier <b>'.$arFichier['fichier'].'</b> a été chargé correctement.</div>';
		else echo '<div class="box-erreur">Une erreur est survenue pendant le chargement du fichier <b>'.$arFichier['fichier'].'</b>.</div>';
	}
}
?>