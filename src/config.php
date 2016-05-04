<?php
/**
 * Fichier de configuration de l'application
 *
 * @author Grégory Heitz
 * @author Damien Mathieu
 */
 
/**
 * Paramètres de configuration du moteur de recherche 
 */
/* Fichiers de dictionnaires */
$cfgSearchDico['blacklist']   = '.\dico\blacklist.txt';    // fichier contenant la liste des mots exclus de la recherche
$cfgSearchDico['similitudes'] = '.\dico\similitudes.txt';  // fichier contenant la liste des fins approchantes
/* Configuration du moteur */
$cfgSearchSeparateurs = "& -_,;'?.:!()|";                  // liste des separateurs de mots
$cfgSearchPluriel = false;                                 // si FALSE alors le caractère 's' en fin de mot est ignoré
$cfgSearchDiacritique = false;                             // si FALSE alors les caractères diacritiques (accents, cédilles...) sont ignorés
$cfgSearchExact = false;								   // si FALSE alors les mots avec une fin approchante sont recherchés
$cfgSearchTailleMot = 1;		                           // taille minimale d'un mot pour être pris en compte dans la recherche (valeur min = 1)
$cfgSearchIntuitive = true;								   // uniquement pour le module de recherche (popup), si FALSE seuls les mots complets sont cherchés, si TRUE les saisies partielles sont acceptées

/**
 * Paramètres de configuration du chargement de fichiers 
 */
$cfgUploadFolder = '.\files';

/**
 * Paramètres de configuration des utilisateurs
 */
$cfgAdministrateur = 'Alice';
$cfgUtilisateur = 'Bob';

/**
 * Paramètres de  configuration des limites de saisie
 */
$cfgAnneeTitreMin = 1900; 									// annee minimale pour les titres
$cfgAnneeTitreMax = 2100; 									// annee maximale pour les titres
														// utiliser = date("Y"); pour limiter à l'année actuelle
$cfgTitreLgMax = 250;
$cfgNomLgMax = 100;
$cfgPrenomLgMax = 50;

/**
 * Paramètres de configuration des fichiers de logs
 */
$cfglogFile = 'log.txt'; // log pour l'import CSV

/**
 * Paramètres d'affichage des x derniers titres / interpretes
 */

$cfgnTopTitres = 10;
$cfgnTopInterpretes = 10;
?>
 