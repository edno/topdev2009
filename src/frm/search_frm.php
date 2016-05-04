<form action="#" method="POST">
	<input type="text" name="search_input" />
	<input type="hidden" name="search_type" value="<?php echo($strSearchType); ?>" />
	<input type="submit" value="Rechercher <?php echo($strSearchType); ?>" />
	<input type="hidden" name="action" value="search" />
</form>
<br />