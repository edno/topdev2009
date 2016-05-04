<?php

require_once 'cook_get_post.php';
require_once 'config.php';

// initialisation de l'utilisateur courant en fonction du niveau (level) et des paramètres de configuration
$user = $level != 'admin' ? $cfgUtilisateur : $cfgAdministrateur;

include_once '.\pages\header.php';
include_once '.\pages\page.php';
include_once '.\pages\footer.php';

?>
