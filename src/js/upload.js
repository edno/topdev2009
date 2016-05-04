function validerFormulaireUpload(nNumber)
{
	var divErreur = document.getElementById('messages');
	var strMsg = new String();
	var bValid = true;
	
	for( var n = 0 ; n < nNumber ; n++ )
	{
		valHiddenFichier = document.getElementsByName('form_fichier' + n)[0].value;
		valSelectTitre = document.getElementsByName('form_titre_select' + n)[0].value;
		valTextTitre = document.getElementsByName('form_titre_input' + n)[0].value;
		valSelectInterprete = document.getElementsByName('form_interprete_select' + n)[0].value;
		valTextNom = document.getElementsByName('form_interprete_nom' + n)[0].value;
		
		if((valSelectTitre == 0 && valTextTitre == "") || (valSelectInterprete == 0 && valTextNom == ""))
		{
			strMsg = strMsg + '<div class="box-erreur">Informations incompl&egrave;tes pour le fichier <b>' + valHiddenFichier + '</b></div>'
			bValid = false;
		}
	}
	if( !bValid) divErreur.innerHTML = strMsg;
	else document.forms[0].submit();
}


function validerFormulaireFichiers()
{
	var divErreur = document.getElementById('messages');
	var arFichiers = document.getElementsByName('form_fichiers[]');
	var bValid = false;
	for( var n=0 ; n<arFichiers.length ; n++)
	{
		if( arFichiers[n].value) bValid = true;
	}
	if( !bValid) divErreur.innerHTML = '<div class="box-erreur">Pas de fichier s&eacute;lectionn&eacute; !</div>';
	else document.forms[0].submit();
}

function validerInputFichier(objFichier)
{
	var divErreur = document.getElementById('messages');
	var arFichiers = document.getElementsByName('form_fichiers[]');
	var bValid = true;

	if( !objFichier.value) return;

	for( var n=0 ; n<arFichiers.length ; n++)
	{
		if( arFichiers[n].id == objFichier.id) continue;
		if( arFichiers[n].value == objFichier.value) bValid = false;
	}
	if( !bValid)
	{
		divErreur.innerHTML = '<div class="box-info">Fichier <b>' + objFichier.value + '</b> d&eacute;j&agrave; s&eacute;lectionn&eacute;.</div>';
		objFichier.value = '';
	}
	else {
		divErreur.innerHTML = '';
		ajouterInputFile('chargement_div');
	}
}

function ajouterInputFile(idForm){
	// recuperation de l'id du formulaire
	var divForm = document.getElementById(idForm);
	// creation d'un div pour contenir l'input
	var divContent   = document.createElement('div');
	// creation de l'input
	var inputFichier = document.createElement('input');
	inputFichier.name = 'form_fichiers[]';
	inputFichier.type = 'file';
	inputFichier.id   = Date.now(); // creation d'un id unique
	inputFichier.onchange = function() { validerInputFichier(this); };
	// ajout de l'input au div conteneur
	divContent.appendChild(inputFichier);
	// ajout du conteneur au div de la page (affichage dans la page)
	divForm.appendChild(divContent);
}