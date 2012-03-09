<?php
/**
* @copyright	Copyright (C) 2008 GWE Systems Ltd. All rights reserved.
 * @license		By negoriation with author via http://www.gwesystems.com
*/
ini_set("display_errors",0);

list($usec, $sec) = explode(" ", microtime());
define('_SC_START', ((float)$usec + (float)$sec));

// Set flag that this is a parent file
define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
$x = realpath(dirname(__FILE__)."/../../") ;
// SVN version
if (!file_exists($x.DS.'includes'.DS.'defines.php')){
	$x = realpath(dirname(__FILE__)."/../../../") ;

}
define( 'JPATH_BASE', $x );

ini_set("display_errors",0);

require_once JPATH_BASE.DS.'includes'.DS.'defines.php';
require_once JPATH_BASE.DS.'includes'.DS.'framework.php';

global $mainframe;
$mainframe =& JFactory::getApplication('site');
$mainframe->initialise();

// use the default layout for the iphone app
setcookie("jevents_view","default",null,"/");
JRequest::setVar("iphoneapp",1);

$script = $_SERVER['REQUEST_URI'];
$urlparts = parse_url($_SERVER['REQUEST_URI']);

$parts = pathinfo($urlparts["path"]);
$filename = $parts["filename"];

$action = JRequest::getWord("action","");
switch ($action){

	case 'iWelcomeText':
		// This is the landing page message
		header('Content-type: text/xml', true);
		$params = JComponentHelper::getParams("com_shines");
		$greeting = htmlspecialchars(nl2br($params->get("greeting","Please configure this message!")),ENT_NOQUOTES);
		
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<WelcomeText>
<Welcome text="<?php echo $greeting;?>" email="feedback@destinshines.com" />
</WelcomeText>
	<?php
	break;

	
	case 'gmap1':
		/**
		 * This is the google maps page
		 * 
	 * Arguments are:
	 * lat = latitude
	 * long = longitude
	 * 
	 */
		$params = JComponentHelper::getParams("com_jevlocations");
		header('Content-type: text/html; charset=utf-8', true);
		$map = file_get_contents(JPATH_SITE."/components/com_shines/ipodhtml/$action.html");
		$map = str_replace("GOOGLEMAPSKEY",$params->get("googlemapskey",""),$map);
		$map = str_replace("{LAT}",JRequest::getFloat("lat",0),$map);
		$map = str_replace("{LON}",JRequest::getFloat("long",0),$map);

		echo $map;
		break;


	case "iEvent";
	/**
	 * the list of today's events
	 * la/lo are only used to compute distance
	 * order is always by time
	 *
	 * Arguments are:
	 * d = YYYY-MM-DD
	 * la = latitude
	 * lo = longitude
	 * 
	 */

	$redirect = "/indexiphone.php?option=com_jevents&task=day.listevents&tmpl=component";

	$d = JRequest::getString("d",false);
	if ($d) {
		list($y,$m,$day) = explode("-",$d);
		$y = intval($y);
		$m = intval($m);
		$day = intval($day);
		$redirect .= "&year=$y&month=$m&day=$day";
	}
	$redirect .= "&iphoneapp=1";
	$la = JRequest::getFloat("la",0);
	$redirect .= "&la=".$la;
	$lo = JRequest::getFloat("lo",0);
	$redirect .= "&lo=".$lo;

	header( 'HTTP/1.1 303 Temporary Redirect' );
	header( 'Location: ' . $redirect );
	exit();

	/*
	header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>
	<TodayEvents>
	<Event id="446" name="Norman Rockwell Holidays" timing="10:00 am-09:00 pm" location="Destin Commons" phone="850-337-8700" lat="30.3912" long="-86.4243" distance="6005.0365235974" community="" />
	<Event id="378" name="Ice Skating Rink at Village of Baytowne Wharf" timing="04:00 pm-10:00 pm" location="The Village of Baytowne Wharf" phone="850-267-8000" lat="30.3949" long="-86.3263" distance="5999.2073952245" community="" />
	<Event id="459" name="MUSIC: The Dream Band" timing="09:00 pm" location="The Village Door" phone="850-502-4590" lat="30.3896" long="-86.3261" distance="5999.1836943148" community="" />
	<Event id="405" name="MUSIC: Zack Rosicka" timing="09:30 pm" location="Funky Blues Shack - Destin" phone="850-654-2764" lat="30.3911" long="-86.4889" distance="6008.8842390174" community="" />
	</TodayEvents>
	<?php
	*/
	break;


	case 'iEventDetails' :
		/**
	 * Arguments are:
	 * id = event id
	 * la/lo are only used to compute distance
	 * la = latitude
	 * lo = longitude
	 * 
	 */

		$redirect = "/indexiphone.php?option=com_jevents&task=icalrepeat.detail&tmpl=component";
		$id = JRequest::getInt("id",0);
		$redirect .= "&evid=".$id;
		$redirect .= "&iphoneapp=1";
		$la = JRequest::getFloat("la",0);
		$redirect .= "&la=".$la;
		$lo = JRequest::getFloat("lo",0);
		$redirect .= "&lo=".$lo;

		header( 'HTTP/1.1 303 Temporary Redirect' );
		header( 'Location: ' . $redirect );
		exit();

		header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<EventDetails address="4300 Legendary Drive, Destin, Florida 32541" url="http://www.destincommons.com">
	<![CDATA[Destin Commons has transformed its streetscape to unveil an educational, interactive "Norman Rockwell Holidays" experience. This is the most unique and memorable holiday display in Northwest Florida, definitely a must see.]]>
</EventDetails>
<?php
break;

// NO iRestaurant


	case 'iRestaurantPage':
		/**
		 * Restaurant list
		 * 
	 * Arguments are:
	 * la = latitude
	 * lo = longitude
	 * bIPhone = iphone - is it an iphone so can make the phone call
	 * name = REstaurant name=1 => search by name
	 * d = distance (units?) - defunct
	 * commid = community id ??? - defunct
	 * alpha = 1 => order alphabetical, 
	 * shake = 1 => random 10 restaurants otherwise nearest
	 * 
	 */		
		$redirect = "/indexiphone.php?option=com_jevlocations&task=locations.listlocations&tmpl=component";

		$redirect .= "&needdistance=1";
		if (!JRequest::getInt("alpha",0)){
			$redirect .= "&sortdistance=1";
		}
		$la = JRequest::getFloat("la",0);
		$redirect .= "&lat=".$la;
		$lo = JRequest::getFloat("lo",0);
		$redirect .= "&lon=".$lo;
		$bIPhone = JRequest::getInt("bIPhone",0);
		$redirect .= "&bIPhone=".$bIPhone;
		$iphoneapp = JRequest::getInt("iphoneapp",0);
		$redirect .= "&iphoneapp=".$iphoneapp;
		// alpha sort
		if (JRequest::getInt("alpha",0)){
			$redirect .= "&filter_order=loc.title&filter_order_Dir=asc";
		}
		else {

		}
		$redirect .= "&limit=0";
		// swtich off filter
		$redirect .= "&jlpriority_fv=0";
		$redirect .= "&filter_loccat=0";

		header( 'HTTP/1.1 303 Temporary Redirect' );
		header( 'Location: ' . $redirect );
		exit();

		header('Content-type: text/html; charset=utf-8', true);
		$map = file_get_contents(JPATH_SITE."/components/com_shines/ipodhtml/$action.html");
		echo $map;
		break;

	case 'iRestaurantDetails' :
		/**
	 * Arguments are:
	 * id = restaurant id
	 * la = latitude
	 * lo = longitude
	 *
	 * la/lo are only used to compute distance
	 * 
	 */
		$id=JRequest::getInt("id",0);
		$redirect = "/indexiphone.php?option=com_jevlocations&task=locations.detail&tmpl=component&loc_id=$id";
		$redirect .= "&iphoneapp=1";
		$la = JRequest::getFloat("la",0);
		$redirect .= "&lat=".$la;
		$lo = JRequest::getFloat("lo",0);
		$redirect .= "&lon=".$lo;

		header( 'HTTP/1.1 303 Temporary Redirect' );
		header( 'Location: ' . $redirect );
		exit();

		header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<RestaurantDetails id="265" name="Wing Stop" lat="30.3881" long="-86.4478" phone="850-837-5333" address="16055 Emerald Coast Parkway" zip="32541" url="http://www.wingstop.com/" community="" distance="6006.4295365056"><![CDATA[It’s no wonder we have sold so many wings after wins at the Buffalo Wing Festival 3 years in a row.  Wingstop has taken chicken wings to a whole new level by saucing and tossing them in a choice of 9 flavors.

 

Wingstop is not fast food; our wi]]></RestaurantDetails>	<?php
break;

// no iCommunity


	case 'iAllAds':
		/**
	 * This gives us the screen names and phone chooses one at random for home page
	 */

		$db = JFactory::getDBO();
		$db->setQuery("SELECT b.*, cat.title as catname FROM #__banner as b LEFT JOIN #__categories as cat ON cat.id=b.catid where b.showBanner=1 AND (b.imptotal=0 OR b.impmade<b.imptotal) AND cat.title IN ('screen','restaurant','main','info','hotspots') ORDER BY RAND()");
		$ads = $db->loadObjectList();
		header('Content-type: text/xml', true);
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<AllAds>';
		// Use Category in place of screen name
		if ($ads) foreach ($ads as $ad){
			$url = JURI::root()."indexiphone.php?option=com_banners&task=click&bid=".$ad->bid;
			echo '<Ad id="'.$ad->bid.'" screen="'.$ad->catname.'" image="'.(JURI::root()."images/banners/".$ad->imageurl).'" type="URL" details="'.htmlspecialchars($url).'" />';
		}
		echo '</AllAds>';
		exit();

		
		header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<AllAds>
<Ad id="22" screen="Search" image="http://www.destinshines.com/cms/Ads/22.gif" type="URL" details="http://destinflsuites.hamptoninn.com" />
<Ad id="40" screen="Restaurant" image="http://www.destinshines.com/cms/Ads/40.gif" type="Restaurant" details="253" />
<Ad id="42" screen="Main" image="http://www.destinshines.com/cms/Ads/42.png" type="URL" details="http://www.jdoqocy.com/click-3692283-10681387" />
<Ad id="36" screen="HotSpots" image="http://www.destinshines.com/cms/Ads/36.gif" type="Restaurant" details="253" />
<Ad id="41" screen="Main" image="http://www.destinshines.com/cms/Ads/41.png" type="URL" details="http://www.dpbolvw.net/click-3692283-10385087" />
<Ad id="43" screen="Info" image="http://www.destinshines.com/cms/Ads/43.png" type="URL" details="http://www.dpbolvw.net/click-3692283-10705964" />
<Ad id="45" screen="Event" image="http://www.destinshines.com/cms/Ads/45.png" type="URL" details="http://www.kqzyfj.com/click-3692283-10445117" />
<Ad id="46" screen="Event" image="http://www.destinshines.com/cms/Ads/46.png" type="URL" details="http://www.kqzyfj.com/click-3692283-10643991" />
</AllAds>
	<?php
	
	break;

	case 'iAdClickView':
		// Not needed - Joomla takes care of this
		break;

	case 'iAd':
		/**
	 * Arguments are:
	 * screeen = from list of screens
	 */
		
		$screen = JRequest::getString("screen","");
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT b.*, cat.title as catname FROM #__banner as b LEFT JOIN #__categories as cat ON cat.id=b.catid where b.showBanner=1 AND (b.imptotal=0 OR b.impmade<b.imptotal) AND cat.title=".$db->Quote($screen)." ORDER BY RAND()");
		$ads = $db->loadObjectList();
		header('Content-type: text/xml', true);
		echo '<?xml version="1.0" encoding="UTF-8"?>';
		echo '<RandomAd>';
		// Use Category in place of screen name
		if ($ads) foreach ($ads as $ad){
			$url = JURI::root()."indexiphone.php?option=com_banners&task=click&bid=".$ad->bid;
			echo '<Ad id="'.$ad->bid.'" image="'.(JURI::root()."images/banners/".$ad->imageurl).'" type="URL" details="'.htmlspecialchars($url).'" />';
		}
		echo '</RandomAd>';
		exit();
		
		header('Content-type: text/xml', true);
	echo '<?xml version="1.0" encoding="UTF-8"?>';?>	
<RandomAd>
	<Ad id="42" image="http://www.destinshines.com/cms/Ads/42.png" type="URL" details="http://www.jdoqocy.com/click-3692283-10681387" />
</RandomAd>	
	<?php
	break;



	case 'iHotSpotCategory' :
		/**
	 * Gives us the list of cateogry names and their ids
	 * cid = category id
	 * 
	 */

		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__categories WHERE section='com_jevlocations2' AND published=1 ORDER BY title");
		$cats = $db->loadObjectList();
		header('Content-type: text/xml', true);
		echo '<?xml version="1.0" encoding="UTF-8"?><HotSpotCategories>'."\n";
		foreach ($cats as $cat) {
			echo '<HotSpotCategory id="'.$cat->id.'" name="'.htmlspecialchars($cat->title, ENT_QUOTES).'" />'."\n";
		}
		echo '</HotSpotCategories>'."\n";
		/*
		?>

		<HotSpotCategories>
		<HotSpotCategory id="11" name="Coffee" />
		<HotSpotCategory id="13" name="Draft Beer" />
		<HotSpotCategory id="5" name="Golf" />
		<HotSpotCategory id="10" name="Ice Cream" />
		<HotSpotCategory id="16" name="Italian Food" />
		<HotSpotCategory id="4" name="Live Music" />
		<HotSpotCategory id="6" name="Movies" />
		<HotSpotCategory id="7" name="Picnic" />
		<HotSpotCategory id="9" name="Pizza" />
		<HotSpotCategory id="3" name="Seafood" />
		<HotSpotCategory id="1" name="Steaks" />
		<HotSpotCategory id="17" name="Sushi" />
		<HotSpotCategory id="15" name="Wine" />
		</HotSpotCategories>
		<?php
		*/
		break;



	case 'iHotSpot' :
		/**
	 * Arguments are:
	 * cid = category id
	 * 
	 */

		$redirect = "/indexiphone.php?option=com_jevlocations&task=locations.listlocations&tmpl=component";

		$cid = JRequest::getInt("cid",0);
		$redirect .= "&filter_loccat=".$cid;

		$redirect .= "&jlpriority_fv=1";
		$redirect .= "&iphoneapp=1";

		header( 'HTTP/1.1 303 Temporary Redirect' );
		header( 'Location: ' . $redirect );
		exit();
		/*
		header('Content-type: text/xml', true);
		echo '<?xml version="1.0" encoding="UTF-8"?>';?>
		<Category id="16" name="Italian Food">
		<HotSpot id="29" name="Guglielmo&apos;s Italian Grill" rank="1" bRestaurant="1" details="288" />
		<HotSpot id="27" name="Graffiti - The Village of Baytowne Wharf" rank="2" bRestaurant="1" details="231" />
		<HotSpot id="28" name="Graffiti - Destin" rank="3" bRestaurant="1" details="230" />
		</Category>
		<?php
		*/
		break;



	case 'iAdPage':
		/**
		 * This is the mini add generator at the bottom of each page.
		 * 
	 * Arguments are:
	 * screen = the screen
	 * 
	 */
		header('Content-type: text/html; charset=utf-8', true);
		$map = file_get_contents(JPATH_SITE."/components/com_shines/ipodhtml/$action.html");
		echo $map;
		break;





	default:
		echo "not done yet : " .$action. " ".$script;
		break;
}



