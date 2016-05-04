<?php
/**
 * Biblioth�que de fonctions g�n�rales
 *
 * @author Gr�gory Heitz
 */

require_once './sql/cfg_sql.php';

/**
 * Fonction Ajout de slashes pour les caract�res sp�ciaux REGEXP
 *
 * @param str chaine � traiter
 * @return chaine trait�e
 */
 function addRegexSlashes(&$str)
 {
	$patterns = array('_', '?', '.', ':', ']', '|');
	$replacements = array('/_', '/?', '/.', '/:', '/]', '/');
	
	$str = str_replace($patterns, $replacements, $str);
	return $str;
}
?>