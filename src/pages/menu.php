<?php
if($level == 'admin')
	{
?>
	<ul class="menu">
		<li><a href ="./index.html">Accueil</a></li>
		<li><a href="?level=admin&menu=interprete">Gestion des interpr&egrave;tes</a></li>
		<li><a href="?level=admin&menu=titre">Gestion des titres</a></li>
		<li><a href="?level=admin&menu=upload">Chargement de fichiers</a></li>
		<li><a href="?level=admin&menu=liste">Liste des fichiers</a></li>
		<li><a href="?level=admin&menu=import">Import de donn&eacute;es</a></li>
	</ul>
<?php
	}
else
{
?>
	<ul class="menu">
		<li><a href ="./index.html">Accueil</a></li>
		<li><a href="?level=user&menu=upload">Chargement de fichiers</a></li>
		<li><a href="?level=user&menu=liste">Liste des fichiers</a></li>
	</ul>
<?php
}
?>

