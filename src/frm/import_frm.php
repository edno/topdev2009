<fieldset>
<legend>Import de données : </legend>
<form method="post" enctype="multipart/form-data" action="">
	<p>Selectionnez votre fichier *.CSV</p>
	<p>
		<input type="file" name="fichierCSV" size="30">
		<input type="submit" name="upload" value="Uploader">
		<input type="hidden" name="action" value="<?php echo 'import' ?>" />
	</p>
</form>
<p><a href="./<?php echo($logFile); ?>" target="_blank" >Log total des imports (.txt)</a></p>
</fieldset>