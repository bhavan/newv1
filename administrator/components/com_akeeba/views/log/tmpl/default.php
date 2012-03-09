<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2006-2010 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id: default.php 71 2010-02-22 22:17:01Z nikosdion $
 * @since 1.3
 */

defined('_JEXEC') or die('Restricted access');
?>

<p style="font-size: large;">
	<blink style="color: red; font-weight: bold; font-size: x-large;">&rArr;</blink>
	<a href="<?php echo JURI::base(); ?>index.php?option=com_akeeba&view=log&task=download&format=raw">
		<?php echo JText::_('LOG_LABEL_DOWNLOAD'); ?>
	</a>
	<blink style="color: red; font-weight: bold; font-size: x-large;">&lArr;</blink>
</p>
<iframe
	src="<?php echo JURI::base(); ?>index.php?option=com_akeeba&view=log&task=iframe&format=raw"
	width="90%" height="400px">
</iframe>