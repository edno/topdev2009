<?php
/**
 * Bibliothèque de fonctions générales
 *
 * @author Grégory Heitz
 */

require_once './sql/cfg_sql.php';

/**
 * Fonction Ajout de slashes pour les caractères spéciaux REGEXP
 *
 * @param str chaine à traiter
 * @return chaine traitée
 */
 function addRegexSlashes(&$str)
 {
	$patterns = array('_', '?', '.', ':', ']', '|');
	$replacements = array('/_', '/?', '/.', '/:', '/]', '/');
	
	$str = str_replace($patterns, $replacements, $str);
	return $str;
}
?>