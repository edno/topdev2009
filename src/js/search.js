function popup(my_page) {
  var L = 500; 
  var H = 450;
  var X = ( screen.width - L )  / 2;
  var Y = ( screen.height - H )  / 2;  
  var OPTIONS = "toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,fullscreen=no,width="+L+",height="+H+",left="+X+",top="+Y;
  var preview;
  preview = window.open(my_page,'recherche',OPTIONS);
  return;
}

function selectTitre(count, id, titre)
{
	opener.document.getElementsByName('form_titre_input' + count)[0].value=titre;
	opener.document.getElementsByName('form_interprete_id' + count)[0].value=id;
}

function selectInterprete(count, id, nom, prenom)
{
	if( prenom != undefined ) opener.document.getElementsByName('form_interprete_prenom' + count)[0].value=prenom;
	opener.document.getElementsByName('form_interprete_nom' + count)[0].value=nom;
	opener.document.getElementsByName('form_interprete_id' + count)[0].value=id;
}