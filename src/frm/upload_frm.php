<fieldset>
<legend>Chargement de fichiers : </legend>
<form action="#" method="POST" enctype="multipart/form-data">
	<p>Selectionnez vos fichiers</p>
	<p>
	<div id="chargement_div">
	<input type="file" name="form_fichiers[]" onChange="validerInputFichier(this);" /><br/>
	</div>
	<input type="hidden" name="action" value="add" />
	<br />
	<input type="button" value="Envoyer" onClick="validerFormulaireFichiers();"/>
	</p>
</form>
</fieldset>