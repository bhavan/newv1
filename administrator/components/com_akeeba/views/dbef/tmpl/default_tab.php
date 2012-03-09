<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: default_tab.php 85 2010-03-11 19:19:51Z nikosdion $
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');
?>

<div id="dialog" title="<?php echo JText::_('DBFILTER_ERROR_TITLE') ?>">
</div>


<div id="ak_top_container" class="ui-corner-tl ui-corner-tr ui-widget-header">
	<div id="ak_roots_container_tab">
		<span><?php echo JText::_('DBFILTER_LABEL_ROOTDIR') ?></span>
		<span><?php echo $this->root_select; ?></span>
	</div>
</div>
<div id="ak_list_container" class="ui-corner-bl ui-corner-br ui-widget-content">
	<table id="ak_list_table" class="adminlist">
		<thead>
			<tr>
				<td width="250px"><?php echo JText::_('FILTERS_LABEL_TYPE') ?></td>
				<td><?php echo JText::_('FILTERS_LABEL_FILTERITEM') ?></td>
			</tr>
		</thead>
		<tbody id="ak_list_contents">
		</tbody>
	</table>
</div>

<script type="text/javascript">
/**
 * Callback function for changing the active root in Filesystem Filters
 */
function akeeba_active_root_changed()
{
	(function($){
		dbfilter_load_tab($('#active_root').val());
	})(akeeba.jQuery);
}

akeeba.jQuery(document).ready(function($){
	// Set the AJAX proxy URL
	akeeba_ajax_url = '<?php echo addcslashes(JURI::base().'index.php?option=com_akeeba&view=dbef&format=raw&task=ajax',"'\\") ?>';
	// Set the media root
	akeeba_ui_theme_root = '<?php echo $this->mediadir ?>';
	// Create the dialog
	$("#dialog").dialog({
		autoOpen: false,
		closeOnEscape: false,
		height: 200,
		width: 300,
		hide: 'slide',
		modal: true,
		position: 'center',
		show: 'slide'
	});
	// Create an AJAX error trap
	akeeba_error_callback = function( message ) {
		var dialog_element = $("#dialog");
		dialog_element.html(''); // Clear the dialog's contents
		dialog_element.dialog('option', 'title', '<?php echo addcslashes(JText::_('CONFIG_UI_AJAXERRORDLG_TITLE'),"'\\") ?>');
		$(document.createElement('p')).html('<?php echo addcslashes(JText::_('CONFIG_UI_AJAXERRORDLG_TEXT'),"'\\") ?>').appendTo(dialog_element);
		$(document.createElement('pre')).html( message ).appendTo(dialog_element);
		dialog_element.dialog('open');
	};
	// Push translations
	akeeba_translations['UI-ROOT'] = '<?php echo addcslashes(JText::_('FILTERS_LABEL_UIROOT'),"'\\") ?>';
	akeeba_translations['UI-ERROR-FILTER'] = '<?php echo addcslashes(JText::_('FILTERS_LABEL_UIERRORFILTER'),"'\\") ?>';
<?php
	$filters = array('tables', 'tabledata');
	foreach($filters as $type)
	{
		echo "\takeeba_translations['UI-FILTERTYPE-".strtoupper($type)."'] = '".
			addcslashes(JText::_('DBFILTER_TYPE_'.strtoupper($type)),"'\\").
			"';\n";
	}
?>
	// Bootstrap the page display
	var data = eval('(<?php echo $this->json; ?>)');
	dbfilter_render_tab(data);
});
</script>