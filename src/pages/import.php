<?php

require_once './sql/cnx_sql.php';
require_once './lib/search_lib.php';
require_once './tpl/import_tpl.php';
require_once './lib/import_lib.php';
require_once './lib/titre_lib.php';
require_once './lib/interprete_lib.php';

define('TMP_DIR','.\tmp\\');

switch($action)
{
	case 'import':
	case 1:
	//recuperation du fichier uploadé
	$fichierCSV = TMP_DIR. $_FILES['fichierCSV']['name'];
	move_uploaded_file($_FILES['fichierCSV']['tmp_name'], $fichierCSV);
	//traitement du fichier
	$buffer = importTraiterCSV($fichierCSV);
	//suppression du fichier apres traitement
	unlink($fichierCSV);
	importAfficherLog($buffer);

	break;
	
	case 'form':
	case 0:
	default:
	importFormulaire();
	break;
}
?>