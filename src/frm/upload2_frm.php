<form action="#" method="POST">

<?php
$count = 0;
foreach($arPropositions as $arFichier) { $fichier = $arFichier['nom'].".".$arFichier['ext']; ?>

	<fieldset>
		<legend><?php echo($fichier); ?></legend>
		<input type='hidden' name='form_fichier<?php echo($count); ?>' value='<?php echo($fichier); ?>'/>
		<table width="100%">
			<tr><th>Titre</th>
				<td><input type='text' name='form_titre_input<?php echo($count); ?>' style='width: 340px;' onClick='document.forms[0].form_titre_select<?php echo($count); ?>.options[0].selected=true;'/></td>
				<td><select name='form_titre_select<?php echo($count); ?>' style='width: 400px; height: 20px;' onChange='document.forms[0].form_titre_input<?php echo($count); ?>.value="";'>
					<option value='0'>
					<?php foreach($arFichier['titres'] as $arTitre) echo('<option value="'.$arTitre['idTitre'].'">'.$arTitre['titreTitre']); ?>
					</select>
				</td>
				<td>
					<a href="#" onClick='popup("index.php?menu=search&flagPopup=1&search_type=titre&count=<?php echo($count); ?>");'><img alt='search' src='img/Knob_Search.png'/></a>
				</td>
			</tr>
			<tr><th>Interprete<br />(pr&eacute;nom, nom)</th>
				<td>
					<input type='text' name='form_interprete_prenom<?php echo($count); ?>' style='width: 160px;' onClick='document.forms[0].form_interprete_select<?php echo($count); ?>.options[0].selected=true;'/>
					<input type='text' name='form_interprete_nom<?php echo($count); ?>' style='width: 160px; margin-right: 10px;' onClick='document.forms[0].form_interprete_select<?php echo($count); ?>.options[0].selected=true;'/>
				</td>
				<td>
					<select name='form_interprete_select<?php echo($count); ?>' style='width: 400px; height: 20px;' onChange='document.forms[0].form_interprete_nom<?php echo($count); ?>.value=""; document.forms[0].form_interprete_prenom<?php echo($count); ?>.value="";'>
					<option value='0'>
					<?php foreach($arFichier['interpretes'] as $arInterprete) echo('<option value="'.$arInterprete['idInterprete'].'">'.$arInterprete['prenomInterprete'].' '.$arInterprete['nomInterprete']); ?>
					</select>
				</td>
				<td>
					<a href="#" onClick='popup("index.php?menu=search&flagPopup=1&search_type=interprete&count=<?php echo($count); ?>");'><img alt='search' src='img/Knob_Search.png'/></a>
				</td>
			</tr>
		</table>
	</fieldset><br/>
	
<?php $count++; } ?>

<input type="hidden" name="form_nbfichiers" value="<?php echo($count); ?>" />
<input type="hidden" name="action" value="edit" />
<input type="button" value="Enregistrer" onClick="validerFormulaireUpload(<?php echo(sizeof($arPropositions)); ?>);"/>
</form>