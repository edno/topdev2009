<form action="#" method="POST">
<?php
global $cfgNomLgMax;
global $cfgPrenomLgMax;
echo '<fieldset>';
echo $nomInterprete ? '<legend>Modifier l\'interprete :</legend>' : '<legend>Ajouter un interprete :</legend>';
//formulaire d'ajout / édition de l'interprete
?>
<p>
	<label> Nom de l'interprete / du groupe </label> : <input type="text" name="formNom" maxlength="
		<?php echo $cfgNomLgMax; ?>"
		<?php if(!empty($nomInterprete)) echo ' value="'.$nomInterprete.'"';?> /></p>
	<p>
	<label> Prénom </label> : <input type="text" name="formPrenom" maxlength="
		<?php echo $cfgPrenomLgMax; ?>"
		<?php if(!empty($prenomInterprete)) echo ' value="'.$prenomInterprete.'"';?>	/></p>
	<input type="hidden" name="action" value="<?php echo $nomInterprete ? 'update' : 'add' ?>" />
	<p><input type="submit" />
</p>
</form>
</fieldset>