<?php

/**
 * Import de fichier CSV
 * Bibliothèque de fonctions
 *
 * @author Damien Mathieu
 */

/**
 * Fonction d'import
 *
 * @param fichier uploadé par formulaire
 * @return texte donnant le statut de chaque opération d'import
 */ 
 
function ImportTraiterCSV($fichierAtraiter)
{
	global $cfglogFile;
	// debut de bufferisation pour log des évenements
	ob_start();
	// Ouverture du fichier csv
	$hCSV = fopen($fichierAtraiter, "r");
	$ligne = 0;
	// parcours du fichier
	while (!feof($hCSV))
	{
	// récupération des variables à transmettre
		$dataFlow = fgetcsv($hCSV, 1000, ";");
		$ligne++;
		$CsvTitre = $dataFlow[2];
		$CsvNomInterprete = $dataFlow[0];
		$CsvPrenomInterprete = $dataFlow[1];
		$CsvOriginal = $dataFlow[3];
	// transmission des éléments à la fonction d'insertion
		ImportInsererLigneCSV($ligne, $CsvTitre, $CsvNomInterprete, $CsvPrenomInterprete, $CsvOriginal);
	}
	fclose($hCSV);

	// enregistrement du buffer dans le fichier de log
	$buffer = ob_get_contents();
	file_put_contents($logFile, $buffer, FILE_APPEND);
	// nettoyage du buffer pour éviter l'affichage à l'écran
	ob_end_clean();
	return $buffer;
}

/**
 * Fonction de traitement de chaque ligne du fichier csv pour insertion en base
 *
 * @param $ligne numéro de la ligne a traiter
 * @param $CsvTitre titre à insérer
 * @param $CsvNomInterprete nom de l'interprete
 * @param $CsvPrenomInterprete prenom de l'interprete
 * @param $CsvOriginal marqueur d'interprete original, valeurs y o yes et oui autorisées, non sensible a la casse
 * @return texte donnant le statut de l'opération d'import
 */ 

function ImportInsererLigneCSV($ligne, $CsvTitre, $CsvNomInterprete, $CsvPrenomInterprete, $CsvOriginal)
{


	echo "-- Import " . $ligne . " -- ".date('Y-m-d H:i:s')."\r\n";
	// si l'entete est reconnue, elle n'est pas traitée
	if ($ligne == 1 AND  $CsvNomInterprete == "Name" AND $CsvPrenomInterprete == "First Name" AND $CsvTitre == "Title" AND $CsvOriginal == "Original")
	{
		
		echo "En-tête du fichier \r\n\r\nIGNORE\r\n";
	}
	//sinon, import des données transmises
	else
	{
	//définition du marqueur d'interprète original
		if (eregi('y|o|yes|oui', $CsvOriginal))
		{
			$interpreteOriginal = 1;
		} 
		else
		{
			$interpreteOriginal = 0;
		}
	// Retour utilisateur sur les valeurs traitées et l'avancement
		echo "Interprete : " . $CsvPrenomInterprete . " " . $CsvNomInterprete . "\r\n";
	// Ajout de l'interprete
		$importInterprete = InterpreteAjouter($CsvNomInterprete, $CsvPrenomInterprete);
	// Retour utilisateur
		echo "Titre : " . $CsvTitre . "\r\n";
	// Ajout du Titre
		$importTitre = TitreAjouter($CsvTitre, '');
	// Couplage des information
		$importCouplage = CoupleAjouterID($importTitre, $importInterprete, $interpreteOriginal);
	// Retour utilisateur final
		if ($importCouplage == false)
		{
			echo "ECHOUE\r\n";
		}
		else
		{
			echo "REUSSI\r\n";
		}
	}
}	
?>