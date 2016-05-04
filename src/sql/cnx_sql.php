<?php
/**
 * Connexion la base de données MySQL
 *
 * @author Grégory Heitz
 */

 // informations de connnexion au serveur mysql
$mysql_cnx['host'] = 'td00808.topdev-test.com';  // adresse du serveur
$mysql_cnx['iden'] = 'td00808';       			 // identifiant de connexion
$mysql_cnx['pass'] = 'pitutu6';           		 // mot de passe de connexion
$mysql_cnx['base'] = 'td00808'; 				 // nom de la base de données

// connexion au serveur MySQL
$hLink = mysql_connect($mysql_cnx['host'], $mysql_cnx['iden'], $mysql_cnx['pass']);
if(!$hLink) {
   die('Connexion au serveur '.$mysql_cnx['host'].' impossible : '.mysql_error());
}

// connexion à la base de données
$hDB = mysql_select_db($mysql_cnx['base'], $hLink);
if(!$hDB) {
   die('Connexion à la base de donnée "'.$mysql_cnx['base'].'" impossible : '.mysql_error());
}
?>
