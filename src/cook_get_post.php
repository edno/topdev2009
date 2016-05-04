<?php
/**
 * Translation du contenu des variable super-globales
 * en variables internes à l'application
 *
 * @author Grégory Heitz
 */

$arGlobal = array ( $_GET, $_POST, $_COOKIE);

foreach($arGlobal as $global) {
	if(is_array($global)) {
		foreach ($global as $key=>$value) {
			$$key = $value;
		}
	}
}

?>
