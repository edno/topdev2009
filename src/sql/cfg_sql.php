<?php
/**
 * Configuration des paramètres pour
 * l'accès aux de données de l'application
 *
 * @author Grégory Heitz
 */

$mysql_data['pfx']    = 'topdev2009_'; // préfixe des tables
$mysql_data['tables'] = array('titres'        => $mysql_data['pfx'].'titres',
                              'interpretes'   => $mysql_data['pfx'].'interpretes',
                              'couples'       => $mysql_data['pfx'].'couples',
                              'fichiers'      => $mysql_data['pfx'].'fichiers',
                              'proprietaires' => $mysql_data['pfx'].'proprietaires'
                             ); // tables de la base de données
$mysql_data['vues']   = array('couplage'      => $mysql_data['pfx'].'couplage'
							 ); // vues de la base de données
?>
