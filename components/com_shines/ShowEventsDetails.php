<?php
 require_once("../../configuration.php");
	 $jconfig = new JConfig();
	 
							 
	  $link = @mysql_pconnect($jconfig->host,  $jconfig->user, $jconfig->password);
	   mysql_select_db($jconfig->db);
	   
	   $rec=mysql_query("select * from `jos_jevents_vevdetail` jjv, `jos_jevents_repetition` jjr where jjv.evdet_id = jjr.eventdetail_id and jjr.rp_id = ".$_REQUEST['evid']);
	   
	     $data =mysql_fetch_array($rec);
		  
		  
	$recloc=mysql_query("select title, street, postcode, city, state, phone, geozoom, geolon, geolat, url from `jos_jev_locations` where `loc_id` = ".$data['location']);
	
	$data['location'] = mysql_fetch_array($recloc);
	$data['location']['url']=str_replace('http://','',$data['location']['url']);
  
 
?>
<EventDetails address="<?php echo $data['location']['street'];?>" url="<?php echo strip_tags('http://'.$data['location']['url']);?>">
	<![CDATA[<?php echo $data['description'];?>]]>
</EventDetails>
<?php exit();