<?php
defined('_JEXEC') or die('Restricted access');

$iphoneapp = JRequest::getInt("iphoneapp",0);
if ($iphoneapp==0) {
	if (isset($this->_path["template"]) && isset($this->_path["template"][1])){
		include($this->_path["template"][1].basename(__FILE__));
		return;
	}
}

$images =  JRequest::getInt("images",0);

ob_end_clean();

if ($images==1){
	header('Content-type: text/xml;charset=utf-8', true);
echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";?>
<photogallery>
<?php
if (!empty($this->items)) {
	foreach($this->items as $key => $item) {
		if ($item->item_type != "image") continue;
		// caption is photo caption " - by ".username
		$uri = JURI::getInstance();
		$root = $uri->toString( array('scheme', 'host', 'port') );
		?><photo id="<?php echo $item->id;?>" caption="<?php echo htmlentities($item->title);?>" url="<?php echo $root.$item->linkorig;?>" />
		<?php
	}
}
/*
?>
<photo id="3" caption="image 3" url="http://www.pcbshines.com/UserPhotos/3.jpg" />
<photo id="4" caption="image 3" url="http://www.pcbshines.com/UserPhotos/4.jpg" />
<?php
*/

?>
</photogallery>
<?php
exit();
}
else {
	header('Content-type: text/xml;charset=utf-8', true);
	$pages =1 + JRequest::getInt("limitstart",0)/25 ;

	echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<AllVideos totalpages="<?php echo $pages;?>">
<?php

if (!empty($this->items)) {
	foreach($this->items as $key => $item) {
		if ($item->item_type != "image") continue;
		if ($item->videocode && $item->videocode!=""){
			$videocode = $item->videocode;
			$dom = new DOMDocument();
			
			@$dom->loadHTML($videocode);
			$params = $dom->getElementsByTagName("param");
			$url = false;
			foreach ($params as $param) {
				if ($param->getAttribute("name")=="movie") {
					$url = str_replace("&","&amp;", $param->getAttribute("value"));
					$url = str_replace("\n","",$url);
					$url = str_replace("\r","",$url);
					$url = str_replace(" ","",$url);
					break;
				}
			}
			if (!$url) continue;
			//echo $videocode;

			$uri = JURI::getInstance();
		$root = $uri->toString( array('scheme', 'host', 'port') );
			
		// nPage = Request.QueryString("pg")
		// caption is photo caption " - by ".username
		?><Video id="<?php echo $item->id;?>" name="<?php echo htmlentities($item->title);?>" desc="<?php echo strip_tags($item->description);?>" thumbnail="<?php echo $root.$item->linkorig;?>" videourl="<?php echo $url;?>" rank="0" />
<?php
		}
	}
}

?>
</AllVideos>
<?php
exit();
}