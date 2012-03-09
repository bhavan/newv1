<?php 
defined('_JEXEC') or die('Restricted access');

function DefaultPaginationForm($total, $limitstart, $limit){
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);
	global $Itemid;
	$task = JRequest::getVar("jevtask");
	$link = JRoute::_("index.php?option=".JEV_COM_COMPONENT."&Itemid=$Itemid&task=$task");
	?>
	<div class="jev_pagination">
	<form action="<?php echo $link;?>" method="post">
	<?php
	echo $pageNav->getListFooter(); 
	?>
	</form>
	</div>
	<?php
}

