<?php defined('_JEXEC') or die('Restricted access'); 

if (!JRequest::getInt("iphoneapp",0)){
	// revert to main layout if not called from iphone
	$maintemplate = dirname(__FILE__)."/../locations2/".basename(__FILE__);
	if ($maintemplate){
		include($maintemplate);
		return;
	}
	else {
		$maintemplate = JPath::find(JPATH_SITE."/components/com_jevlocations/views/locations/tmpl/",basename(__FILE__));
		if ($maintemplate){
			include($maintemplate);
			return;
		}
	}	
}

$lat = JRequest::getFloat("lat",0);
$lon = JRequest::getFloat("lon",0);
if ($lat==0 && $lon==0){
	$lat = JRequest::getFloat("la",0);
	$lon = JRequest::getFloat("lo",0);
}

$distance = (((acos(sin(($lat * pi() / 180)) * sin(($this->location->geolat * pi() / 180)) + cos(($lat * pi() / 180)) * cos(($this->location->geolat * pi() / 180)) * cos((($lon - $this->location->geolon) * pi() / 180)))) * 180 / pi()) * 60 * 1.1515);
$this->location->phone=str_replace('(','',$this->location->phone);
$this->location->phone=str_replace(')','-',$this->location->phone);
$this->location->phone=str_replace(' ','',$this->location->phone);

ob_end_clean();


header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<RestaurantDetails 
id="<?php echo $this->location->loc_id;?>" 
name="<?php echo $this->location->title;?>" 
lat="<?php echo $this->location->geolat;?>" 
long="<?php echo $this->location->geolon;?>" 
phone="<?php echo $this->location->phone;?>" 
address="<?php echo $this->location->street.", ".$this->location->city.", ".$this->location->state.", ";?>" 
zip="<?php echo $this->location->postcode;?>" 
<?php
if ($this->location->url!="" && strpos($this->location->url,"http://")===false) {
	$this->location->url= "http://".$this->location->url;
}
?>
url="<?php echo $this->location->url;?>" 
community="" 
distance="<?php echo round($distance,1); //echo round(JRequest::getFloat("d",0),1);?>">
<![CDATA[<?php echo  $this->location->description;?>]]></RestaurantDetails>
<?php
exit();