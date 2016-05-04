<?php
// template d'affichage des titres

function TitreAfficher($arTitre, $nResults=1)
{
	if (!empty($arTitre))
	{
	global $cfgnTopTitres;
	global $action;
	echo '<fieldset>';
	if($action == 'search' AND $nResults == 1)
	{
		echo '<legend>Résultat de la recherche :</legend>';
	}
	else
	{
		echo '<legend>' . $cfgnTopTitres . ' derniers titres enregistrés en base :</legend>';
	}
	?>
	<table>
	<thead><th>Titre</th><th width="60px">Ann&eacute;e</th><th>Interpr&egrave;te Original</th><th width="45px">Supprimer</th></thead>
	<tbody>
	<?php //reprise des informations relatives aux titres sous forme de tableau
	foreach ($arTitre as $arRetour)
	{
		if($arRetour['anneeTitre'] == '0000'){$arRetour['anneeTitre'] = '----';}
		echo '<tr>
		<td><a href="./?level=admin&menu=titre&action=mod&idTitre=' . $arRetour['idTitre'] . '">' . $arRetour['titreTitre'] . '</a></td>
		<td class="center">' . $arRetour['anneeTitre'] . '</td>
		<td>' . $arRetour['interpreteOriTitre'] . '</td>
		<td class="center"><a href="./?level=admin&menu=titre&action=delete
			&idTitre='.$arRetour['idTitre'].'" 
			onclick="javascript:return confirm(\'       ATTENTION \r\n Tous les couples associés seront SUPPRIMES \r\n Souhaitez vous poursuivre ?\')"><img src="./img/b_drop.png" alt="Supprimer" ></a></td>
		</tr>';
	}
	?>
	</tbody>
	</table>
	</fieldset>
	<?php
	}
}
// affichage du formulaire d'ajout
function TitreFormulaire($arTitreMod)
{
	include './frm/titre_frm.php';
}
// affichage du formulaire de recherche
function TitreRechercher()
{
	require './frm/titre_rch_frm.php';
}
?>