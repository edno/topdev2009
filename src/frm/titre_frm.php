<form action="#" method="POST">
<?php
global $cfgTitreLgMax;
// si aucune modification n'est demandée - cas de l'ajout
if(empty($arTitreMod))
	{
		
		echo '<fieldset><legend>Ajouter un titre :</legend>
		<label> Titre du morceau </label> : <input type="text" name="formTitre" maxlength="'.$TitreLgMax.'"	/></p>
		<p><label> Ann&eacute;e (facultatif) </label> : <input type="text" name="formAnnee" /></p>';
	?>
		<label> Interpr&egrave;te : </label><input type='text' name='form_interprete_prenom0' readonly />
		<input type='text' name='form_interprete_nom0' readonly />
		<input type='hidden' name='form_interprete_id0' />
		<a href="#" onClick='popup("index.php?menu=search&flagPopup=1&search_type=interprete&count=0");'><img alt='search' src='img/Knob_Search.png'/></a>
	<?php
	}
	else //sinon, modification : rappel des infos existantes et génération d'un tableau
	{
		echo '<fieldset> <legend>' . $arTitreMod[0]['titreTitre'] . '</legend>';
		echo '<p><label>Modifier le nom du titre </label> : <input type="text" name="formTitre" maxlength="'.$TitreLgMax.'"
		 value="'.$arTitreMod[0]['titreTitre'].'" /></p>';
		echo '<p><label>Modifier l\'année du titre </label> : <input type="text" name="formAnnee" 
		value="'.$arTitreMod[0]['anneeTitre'].'" /></p>';
		echo '<h5>Couples Titre / Interprètes connus : </h5>';
		if (isset($arTitreMod[0]['interpreteOriginal']))
		{
			echo '<table><thead><th>Nom Interprete</th><th>Prenom Interprete</th><th>Interprete Original</th><th>Supprimer</th></thead>';
			foreach($arTitreMod as $arInterprete)
			{
			?>
			<tr><td>
			<?php
			echo $arInterprete['nomInterprete'];
			?>
			</td>
			<td> 
			<?php
			echo $arInterprete['prenomInterprete'];
			?>	
			</td>
			<td>
			<input type="radio" name="idNewCouple"
			<?php
				if($arInterprete['interpreteOriginal'] == 1)
				{
					echo 'checked="checked"';
				}
			echo ' value ="' . $arInterprete['idCouplage'] . '" /></td>';
			echo '<td><input type="checkbox" name="deleteCouplage[' . $arInterprete['idCouplage'] . ']" /></td></tr>';
				if ($arInterprete['interpreteOriginal'] == 1)
				{
					echo '<input type="hidden" name="idOldCouple" value="';
					echo $arInterprete['idCouplage'];
					echo '" />';
				}
			}
			echo '</table>';
		}
		echo '<p>Ajouter un interprete (non original) :</p>';
		?>
		<input type='text' name='form_interprete_prenom0' readonly />
		<input type='text' name='form_interprete_nom0' readonly />
		<input type='hidden' name='form_interprete_id0' />
		<a href="#" onClick='popup("index.php?menu=search&flagPopup=1&search_type=interprete&count=0");'><img alt='search' src='img/Knob_Search.png'/></a>
		<?php
		echo '<input type="hidden" name="idTitre" value="' . $arTitreMod[0]['idTitre'] . '" />';
	}
?>
<input type="hidden" name="action" value="<?php echo $arTitreMod ? 'updatecpl' : 'add' ?>" />
<p><input type="submit" value="Envoyer" /></p>
</form>
</fieldset>