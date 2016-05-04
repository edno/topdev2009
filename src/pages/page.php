<?php if(!isset($flagPopup) || $flagPopup != 1) { ?>
<div id="menugauche">
<?php include './pages/menu.php'; ?>
</div>
<?php } ?>

<div id="contenu">
<?php require_once './tpl/contenu_tpl.php'; ?>
</div>