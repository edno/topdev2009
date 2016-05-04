<?php

require_once './sql/cnx_sql.php';
require_once './lib/search_lib.php';
require_once './tpl/search_tpl.php';

RechercheAfficherFormulaire($search_type);
$arResultats = array();
switch($search_type) {
	case 'interprete':
		if( isset($search_input)) $arResultats = RechercheLireInterpretes($search_input, 0, false, false, $cfgSearchIntuitive);
		RechercheAfficherResultatInterpretes($arResultats);
		break;
	
	case 'titre':
		if( isset($search_input)) $arResultats = RechercheLireTitres($search_input, 0, false, false, $cfgSearchIntuitive);
		RechercheAfficherResultatTitres($arResultats);
		break;
}