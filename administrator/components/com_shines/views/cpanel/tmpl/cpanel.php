<?php 
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: cpanel.php 1603 2009-10-12 08:59:30Z geraint $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2009 GWE Systems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */
defined('_JEXEC') or die('Restricted access'); ?>
<div id="jevents">
   <?php if (isset($this->warning)){
   	?>
		<dl id="system-message">
		<dt class="notice">Message</dt>
		<dd class="notice fade">
			<ul>
				<li><?php echo $this->warning;?></li>
			</ul>
		</dd>
		</dl>   	
   	<?php
   }
   ?>
   <form action="index.php" method="post" name="adminForm" >
	<table width="90%" border="0" cellpadding="2" cellspacing="2" class="adminform">
	
		<tr>
			<td width="55%" valign="top">
				<div id="cpanel">
					<div style="float:left;">
						<div class="icon">
							<?php $link = "index.php?option=com_config&controller=component&component=com_shines";?>
							<a href="<?php echo $link; ?>" rel="{handler: 'iframe', size: {x: 750, y: 580}}" class="modal">
								<?php echo JHTML::_('image.administrator', "icon-48-config.png", "/administrator/templates/khepri/images/header/", NULL, NULL, JText::_('Parameters')  ); ?>
								<span><?php echo JText::_('Parameters') ; ?></span>
							</a>
						</div>
					</div>
				</div>
			</td>
			<td width="45%" valign="top">
			</td>
		</tr>
  </table>

  <input type="hidden" name="task" value="cpanel" />
  <input type="hidden" name="act" value="" />
  <input type="hidden" name="option" value="com_shines" />
</form>
</div>
