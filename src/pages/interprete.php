<?php

require_once './sql/cnx_sql.php';
require_once './lib/interprete_lib.php';
require_once './tpl/interprete_tpl.php';
require_once './lib/search_lib.php';

switch($action) {

	case 'add':
	case 1:
	// ajout d'interprete
	InterpreteAjouter($formNom, $formPrenom);
	InterpreteFormulaire('');
	InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes));
	InterpreteRechercher();

	break;
	
	case 'modify':
	case 2:
	// transmission au formulaire des informations de l'interprete � modifier
	InterpreteFormulaire($modNom, $modPrenom, $modId);
	break;

	case 'update':
	case 3:
	// mise � jour de l'interprete
	InterpreteMaj($modId, $formNom, $formPrenom);
	InterpreteFormulaire('');
	InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes));
	InterpreteRechercher();

	break;
	
	case 'delete':
	case 4:
	// suppressiond d'un interprete
	if(!InterpreteSupprimer($idInterprete))
	{echo '<div class="box-erreur">Impossible de supprimer un interpr�te associ� � des titres.</div>';}
	InterpreteFormulaire('');	
	InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes));
	InterpreteRechercher();
	break;
	
	case 'search':
	case 5:
	InterpreteFormulaire('');
	// si le racherche ne comporte pas assez de caract�res
	if(strlen($formSearchInterprete) < 2 AND !empty($formSearchInterprete))
	{
		echo '<div class="box-erreur">La recherche doit comporter 2 caract�res minimum.</div>';
		InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes), 0);
	}
	else
	{// sinon recherche de la chaine demand�e
		$rechercheInterprete = 	RechercheLireInterpretes($formSearchInterprete);
		if(empty($rechercheInterprete))
		{// aucun r�sultat, on affiche les x derniers enregistr�s
			echo '<div class="box-erreur">Aucun r�sultat trouv� pour ' . $formSearchInterprete . '.</div>';
			InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes), 0);
		}
		else
		{// affichage des r�sultats de la recherche
			InterpreteAfficher($rechercheInterprete);
		}
	}
	InterpreteRechercher();
	break;
	
	case 'show-form':
	case 0:
	default:
	// affichage par d�faut
	InterpreteFormulaire('');	
	InterpreteAfficher(RechercheLireInterpretes('', $cfgnTopInterpretes));
	InterpreteRechercher();
	break;
}
?>