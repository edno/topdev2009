<?php
/**
 * Fichier de configuration de l'application
 *
 * @author Gr�gory Heitz
 * @author Damien Mathieu
 */
 
/**
 * Param�tres de configuration du moteur de recherche 
 */
/* Fichiers de dictionnaires */
$cfgSearchDico['blacklist']   = '.\dico\blacklist.txt';    // fichier contenant la liste des mots exclus de la recherche
$cfgSearchDico['similitudes'] = '.\dico\similitudes.txt';  // fichier contenant la liste des fins approchantes
/* Configuration du moteur */
$cfgSearchSeparateurs = "& -_,;'?.:!()|";                  // liste des separateurs de mots
$cfgSearchPluriel = false;                                 // si FALSE alors le caract�re 's' en fin de mot est ignor�
$cfgSearchDiacritique = false;                             // si FALSE alors les caract�res diacritiques (accents, c�dilles...) sont ignor�s
$cfgSearchExact = false;								   // si FALSE alors les mots avec une fin approchante sont recherch�s
$cfgSearchTailleMot = 1;		                           // taille minimale d'un mot pour �tre pris en compte dans la recherche (valeur min = 1)
$cfgSearchIntuitive = true;								   // uniquement pour le module de recherche (popup), si FALSE seuls les mots complets sont cherch�s, si TRUE les saisies partielles sont accept�es

/**
 * Param�tres de configuration du chargement de fichiers 
 */
$cfgUploadFolder = '.\files';

/**
 * Param�tres de configuration des utilisateurs
 */
$cfgAdministrateur = 'Alice';
$cfgUtilisateur = 'Bob';

/**
 * Param�tres de  configuration des limites de saisie
 */
$cfgAnneeTitreMin = 1900; 									// annee minimale pour les titres
$cfgAnneeTitreMax = 2100; 									// annee maximale pour les titres
														// utiliser = date("Y"); pour limiter � l'ann�e actuelle
$cfgTitreLgMax = 250;
$cfgNomLgMax = 100;
$cfgPrenomLgMax = 50;

/**
 * Param�tres de configuration des fichiers de logs
 */
$cfglogFile = 'log.txt'; // log pour l'import CSV

/**
 * Param�tres d'affichage des x derniers titres / interpretes
 */

$cfgnTopTitres = 10;
$cfgnTopInterpretes = 10;
?>
 