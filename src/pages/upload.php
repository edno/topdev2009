<?php

require_once './sql/cnx_sql.php';
require_once './lib/upload_lib.php';
require_once './tpl/upload_tpl.php';

switch($action) {
	case 'add':
	case 2:
		$arFichiers = UploadChargerMultiFichiers($_FILES['form_fichiers'], $user);
		UploadFormulaireEdition($arFichiers);
		break;
		
	case 'edit':
	case 3:
		// récuperations des paramètres du formalaire dans un tableau
		$arFichiers = array();
		for($n=0 ; $n < $form_nbfichiers ; $n++)
		{
			array_push($arFichiers, array( 'fichier'      => ${"form_fichier{$n}"},
										   'titre'        => ${"form_titre_input{$n}"},
										   'idTitre'      => intval(${"form_titre_select{$n}"}),
										   'nom'	      => ${"form_interprete_nom{$n}"},
										   'prenom'	      => ${"form_interprete_prenom{$n}"},
										   'idInterprete' => intval(${"form_interprete_select{$n}"})
										  ));
		}
		$arRetour = UploadDeclarerFichiers($arFichiers, $user);
		// affichage du statut de chargement des fichiers
		UploadAffichierEtatChargement($arRetour);
		// suppression des fichiers locaux qui n'ont pas été déclarés correctement
		foreach( $arRetour as $arFile) 
			if( !$arFile['status']) 
			{
				UploadSupprimerFichierLocal($arFile['fichier']);
				UploadSupprimerFichierBase($arFile['fichier']);
			}
		break;

	case 'show':
	case 1:
		$arFichiers = UploadListerFichiers($user, $level);
		UploadAfficherFichiers($arFichiers);
		break;		
		
	case 'form':
	case 0:
	default:
		UploadFormulaireImport();
		break;
}
?>