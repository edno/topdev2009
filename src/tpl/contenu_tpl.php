<div id="messages"><!-- Zone d'affichage des messages --></div>

<?php 
if( !isset($menu)) $menu='';
if( !isset($action)) $action='';

switch($menu) {
	case 1:
	case 'interprete':
		$page = 'interprete.php';
		break;
	
	case 2:
	case 'titre':
		$page = 'titre.php';
		break;
		
	case 3:
	case 'upload':
		$page = 'upload.php';
		break;

	case 4:
	case 'import':
		$page = 'import.php';
		break;	
		
	case 5:
	case 'search':
		$page = 'search.php';
		break;			
		
	case 0:
	case 'liste':
	default:
		$page = 'upload.php';
		$action = 'show';
		break;
	}

	include './pages/'.$page;
?>