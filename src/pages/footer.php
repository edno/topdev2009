<?php if(!isset($flagPopup) || $flagPopup != 1) { ?>
		<div id="pied">
		<?php echo $user, ' (', ($level == 'admin' ? 'gestionnaire' : 'utilisateur') , ')'; ?>
		</div>
<?php } ?>

	</div>

</body>
</html>
