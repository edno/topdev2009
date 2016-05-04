<?php
// template d'affichage des interpretes

function InterpreteAfficher($arInterprete, $nResults=1)
{
	global $action;
	global $cfgnTopInterpretes;
	echo '<fieldset>';
	if (!empty($arInterprete))
	{
		if($action == 'search' AND $nResults == 1)
		{
			echo '<legend>Résultat de la recherche :</legend>';
		}
		else
		{
			echo '<legend>' . $cfgnTopInterpretes . ' derniers interprètes enregistrés en base :</legend>';
		}
?>
		<table>
		<thead><th>Pr&eacute;nom</th><th>Nom</th><th width="45px">Modifier</th><th width="45px">Supprimer</th></thead>
		<tbody>
		<?php //mise en forme de la liste des interpretes
		foreach($arInterprete as $arRetour)
		{
			echo '<tr><td>' . $arRetour['prenomInterprete'] . '</td>
				<td>' . $arRetour['nomInterprete'] . '</td>
				<td class="center"><a href="./?level=admin&menu=interprete&action=modify
					&modNom='.$arRetour['nomInterprete'].'
					&modPrenom='.$arRetour['prenomInterprete'].'
					&modId='.$arRetour['idInterprete'].'" ><img src="./img/b_edit.png" alt="Modifier" ></a>
				</td>
				<td class="center"><a href="./?level=admin&menu=interprete&action=delete
				&idInterprete='.$arRetour['idInterprete'].'"
onclick="javascript:return confirm(\'Etes vous sûr de vouloir supprimer cet enregistrement ?\')"><img src="./img/b_drop.png" alt="Supprimer" ></a></td>
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
function InterpreteFormulaire($nomInterprete, $prenomInterprete='', $idInterprete='')
{
	require './frm/interprete_frm.php';
}
// affichage du formulaire de recherche
function InterpreteRechercher()
{
	require './frm/interprete_rch_frm.php';
}