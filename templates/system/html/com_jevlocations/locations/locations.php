<?php defined('_JEXEC') or die('Restricted access'); 

if ( JRequest::getInt("jlpriority_fv",0)>0){
	echo $this->loadTemplate('hotspot');
	return;
}

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
ob_end_clean();
header( 'Content-Type:text/html;charset=utf-8');
?>
<html>
	<head>
		<meta name="viewport" content="width=310, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<script language="javascript">
		function linkClicked(link) { document.location = link; }
		</script>
		<style>
			body { margin-top: 1px; margin-left: 0px; margin-right: 0px; font-family: Helvetica, Arial, sans-serif; }
			.bluetext { color: #0088BB; font-size: 13px; font-weight:bold; }
			.bluetextsmall { color: #00AADD; font-size: 13px; /*font-style: italic;*/}
			.headertext { color: #000000; font-size: 17px; }
			.graytext { color: #777777; font-size: 14px; }
			.graytextSmall { color: #777777; font-size: 13px; }
			.linktext { color: blue; font-size: 14px; text-decoration: underline; } 
		</style>
	</head>
	<body>
	<?php if (count( $this->items)) { ?>
		<table width="310" cellpadding="0" cellspacing="0" border="0">
		<?php 

		$k = 0;
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = &$this->items[$i];

			if ($this->usecats){
				if(isset($row->c3title)){
					$country = $row->c3title;
					$province = $row->c2title;
					$city = $row->c1title;
				}
				else if(isset($row->c2title)){
					$country = $row->c2title;
					$province = $row->c1title;
					$city = false;
				}
				else {
					$country = $row->c1title;
					$province = false;
					$city = false;
				}
			}
			else {
				$country = $row->country;
				$province = $row->state;
				$city = $row->city;
			}
			$distance = $row->distance;
			$la = JRequest::getFloat("lat",0);
			$lo = JRequest::getFloat("lon",0);
		?>
		<tr >
			<td style="width:260px;border-top:solid 1px #009dd9">
	            <table cellpadding="1" cellspacing="0">
		            <tr><td style="height:3px"></td></tr>
		            <tr><td class="headertext"><?php echo $this->escape($row->title); ?></td></tr>		            
		            <tr><td class="graytext"><?php 
		            $words = str_word_count(strip_tags($row->description),1);
		            echo htmlspecialchars(implode(" ",array_slice($words,0,30)));
		            ?></td></tr>
		            <tr><td class="graytext">
		            	<?php if (JRequest::getInt("bIPhone",0)){?>
                        <a class="linktext" href="javascript:linkClicked('APP30A:MAKECALL:<?php echo $this->escape($row->phone); ?>')"><?php echo $this->escape($row->phone); ?></a> |     
                      	<?php } else { 
                      		echo $this->escape($row->phone)." | ";
                      	 } ?>
			            <a class="linktext" href="javascript:linkClicked('APP30A:SHOWDETAILS:<?php echo $row->loc_id; ?>:<?php echo round($distance,1); ?>')">more info</a>
			            <a class="linktext" href="javascript:linkClicked('APP30A:SHOWMAP:<?php echo $row->geolon; ?>:<?php echo $row->geolat; ?>')"></a>
			        	</td>
			        </tr>
		            <tr><td style="height:5px"></td></tr>
	            </table>
			</td>
			<td class="graytext" width="50px" style="border-top:solid 1px #009dd9" valign="middle" align="center"><?php echo round($distance,1); ?> miles</td>			
		</tr>			

		<?php
		$k = 1 - $k;
		}
?>
		            
		</table>
		<?php } ?>
	</body>
</html>
<?php
exit();