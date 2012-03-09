<?php 
defined('_JEXEC') or die('Restricted access');

$cfg	= & JEVConfig::getInstance();

if( 0 == $this->evid || is_null($this->data) || !array_key_exists('row',$this->data)){
	header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>
	<EventDetails></EventDetails>
	<?php
}

$row=$this->data['row'];

/*
		header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<EventDetails address="4300 Legendary Drive, Destin, Florida 32541" url="http://www.destincommons.com">
	<![CDATA[xx2 Destin Commons has transformed its streetscape to unveil an educational, interactive "Norman Rockwell Holidays" experience. This is the most unique and memorable holiday display in Northwest Florida, definitely a must see.]]>
</EventDetails>
<?php exit();
*/
ob_end_clean();
header('Content-type: text/xml', true);
echo '<?xml version="1.0" encoding="UTF-8"?>';
$location = $row->_jevlocation;
$url = strip_tags($location->url);
?>	
<EventDetails address="<?php echo $location->street;?>" url="<?php echo $url;?>">
	<![CDATA[<?php echo $row->content();?>]]>
</EventDetails>
<?php exit();
