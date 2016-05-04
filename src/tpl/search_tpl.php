<?php
/**
 * Moteur de recherche
 * Affichage des données
 *
 * @author Grégory Heitz
 */

/**
 * Fonction d'affichage des résultats de recherche de titre
 *
 * @param arResults tableau de résultats issu de la fonction RechercheLireTitres
 */
function RechercheAfficherResultatTitres($arResults=null)
{
	global $count; // variable passée en paramètre à l'ouverture de la fenêtre de recherche

	if( !$arResults) 
	{
		echo '<div class="box-info">Aucun titre ne correspond &agrave; votre recherche.</div>';
		return;
	}

	$strText = sizeof($arResults) > 1 ? sizeof($arResults).' titres correspondent' : 'Un titre correspond';
	echo '<div class="box-info">', $strText, ' &agrave; votre recherche.</div>';
	foreach( $arResults as $arItem)
	{
		echo "<div class='box-result' onClick='selectTitre(", $count, ",", $arItem['idTitre'], ",\"", $arItem['titreTitre'], "\");' onmouseover='this.className=\"box-result_over\";' onmouseout='this.className=\"box-result\";'>"
			, $arItem['titreTitre'], '</a></div>';
	}
}

/**
 * Fonction d'affichage des résultats de recherche d'interprete
 *
 * @param arResults tableau de résultats issu de la fonction RechercheLireInterpretes
 */
function RechercheAfficherResultatInterpretes($arResults)
{
	global $count; // variable passée en paramètre à l'ouverture de la fenêtre de recherche

	if( !$arResults || empty($arResults)) 
	{
		echo '<div class="box-info">Aucun interpr&egrave;te ne correspond &agrave; votre recherche.</div>';
		return;
	}
	
	$strText = sizeof($arResults) > 1 ? sizeof($arResults).' interpr&egrave;tes correspondent' : 'Un interpr&egrave;te correspond';
	echo '<div class="box-info">', $strText, ' &agrave; votre recherche.</div>';	
	foreach( $arResults as $arItem)
	{
		$prenom = empty($arItem['prenomInterprete']) ? null : ",\"".$arItem['prenomInterprete']."\"";
		echo "<div class='box-result' onClick='selectInterprete(", $count, ",", $arItem['idInterprete'], ",\"", $arItem['nomInterprete'], "\"", $prenom, ");' onmouseover='this.className=\"box-result_over\";' onmouseout='this.className=\"box-result\";'>"
			, $arItem['prenomInterprete'], " ", $arItem['nomInterprete']
			, "</div>";
	}
}

/**
 * Fonction d'affichage du formulaire de recherche
 *
 * @param strSearchType chaine correspondant au type de recherche (titre | interprete)
 */
function RechercheAfficherFormulaire($strSearchType)
{
	include '.\frm\search_frm.php';
}
?>