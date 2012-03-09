<?php
// ensure this file is being included by a parent file
defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_juga {
	function jugaMain( $option ) {
		// simple spoof check
		$validate = josSpoofValue();
		
		mosCommonHTML::loadOverlib();	
		?>
		<script language="javascript" type="text/javascript">
		function submitbutton( pressbutton ) {
			var form = document.mosUserForm;
			var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

			// do field validation
			if (form.jugacode.value == "") {
				alert( "<?php echo _juga_invalid_code; ?>" );
			} else if (r.exec(form.jugacode.value) || form.jugacode.value.length < 3) {
				alert( "<?php echo _juga_invalid_code; ?>" );
			} else {
				form.submit();
			}
		}
		</script>
		<form action="index.php" method="post" name="mosUserForm">
		<div class="componentheading">
			<?php echo _juga_code; ?>
		</div>
		<table cellpadding="5" cellspacing="0" border="0" width="100%">
		<tr>
			<td width="85">
				<?php echo _juga_code; ?>: 
			</td>
			<td>
				<input class="inputbox" type="text" name="jugacode" value="" size="40" />
			</td>
		</tr>
		<tr><td colspan="2">
			<div class="back_button">
			<a href="javascript:submitbutton('processcode');" ><?php echo _JUGA_SUBMIT; ?></a>
			</div>
		</td></tr>
		</table>
		<input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="processcode" />
		<input type="hidden" name="<?php echo $validate; ?>" value="1" />
		</form>
		<?php
	}
}
?>
