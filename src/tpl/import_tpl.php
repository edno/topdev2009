<?php

function importFormulaire()
{
	global $cfglogFile;
	include './frm/import_frm.php';
}

function importAfficherLog($buffer)
{
	$arLog = explode("\r\n", $buffer);

	for($n=0; $n < sizeof($arLog); $n+=4)
	{
		if (empty($arLog[$n])) continue;
		if($arLog[$n+3] == 'ECHOUE')
			{
			echo '<div class="box-erreur"> Import de ' . $arLog[$n+2] . ' par ' . $arLog[$n+1] . ' : ' . $arLog[$n+3] . '</div>';
			}
		elseif($arLog[$n+3] == 'REUSSI')
		{
			echo '<div class="box-succes"> Import de ' . $arLog[$n+2] . ' par ' . $arLog[$n+1] . ' : ' . $arLog[$n+3] . '</div>';
		}
		else
		{
			echo '<div class="box-info"> ' . $arLog[$n+2] . ' ' . $arLog[$n+1] . ' : ' . $arLog[$n+3] . '</div>';
		}
	}
}
?>