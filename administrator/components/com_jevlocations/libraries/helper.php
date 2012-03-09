<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 * @version     $Id: categoryClass.php 1117 2008-07-06 17:20:59Z tstahl $
 * @package     JEvents
 * @copyright   Copyright (C) 2006-2008 JEvents Project Group
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://joomlacode.org/gf/project/jevents
 */

defined( '_JEXEC' ) or die( 'Restricted access' );


class JevLocationsHelper {

	private $params;

	function __construct(){
		$this->params = JComponentHelper::getParams("com_jevlocations");
	}

	static function canCreateOwn() {
		$params =& JComponentHelper::getParams('com_jevents');
		$authorisedonly = $params->get("authorisedonly",0);
		if (!$authorisedonly){
			$params =& JComponentHelper::getParams("com_jevlocations");
			$loc_own = $params->get("loc_own",25);
			$juser =& JFactory::getUser();
			if ($juser->gid>=intval($loc_own)){
				return true;
			}
		}
		else {
			$jevuser =& JEVHelper::getAuthorisedUser();
			if 	($jevuser && $jevuser->cancreateown){
				// if jevents is not in authorised only mode then switch off this user's permissions
				return true;
			}
		}
		return false;
	}

	static function canCreateGlobal() {
		$params =& JComponentHelper::getParams('com_jevents');
		$authorisedonly = $params->get("authorisedonly",0);
		if (!$authorisedonly){
			$params =& JComponentHelper::getParams("com_jevlocations");
			$loc_global = $params->get("loc_global",25);
			$juser =& JFactory::getUser();
			if ($juser->gid>=intval($loc_global)){
				return true;
			}
			
			// special case for anonymous users
			$params =& JComponentHelper::getParams("com_jevlocations");
			$anoncreate = $params->get("anoncreate",25);
			$user = JFactory::getUser();
			if ($anoncreate && $user->id==0 && ( JRequest::getString("task","")=="locations.save" || JRequest::getString("task","")=="locations.select")){
				return true;
			}

		}
		else {
			$jevuser =& JEVHelper::getAuthorisedUser();
			if 	($jevuser && $jevuser->cancreateglobal){
				// if jevents is not in authorised only mode then switch off this user's permissions
				return true;
			}
			if (is_null($jevuser)){
				// special case for anonymous users
				$params =& JComponentHelper::getParams("com_jevlocations");
				$anoncreate = $params->get("anoncreate",25);
				$user = JFactory::getUser();
				if ($anoncreate && $user->id==0 && ( JRequest::getString("task","")=="locations.save" || JRequest::getString("task","")=="locations.select")){
					return true;
				}
			}
		}
		return false;
	}
	
	static function getApiKey(){
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$googlekey = $compparams->get("googlemapskey","");
		
		$uri = JURI::getInstance();
		$domain = $uri->toString(array('host'));
		
		$testdomain = "";
		$testkey = "";
		for ($i=1;$i<=10;$i++){
			$testdomain = $compparams->get("googledomain".$i,"");
			if ($testdomain=="") break;
			$testkey = $compparams->get("googledomainkey".$i,"");			
			if ($domain == $testdomain){
				return $testkey;
			}
		}
		return $googlekey;
	}
	
	static function getApiUrl(){
		$compparams = JComponentHelper::getParams("com_jevlocations");
		$googleurl = $compparams->get("googlemaps",'http://maps.google.com');
		
		$uri = JURI::getInstance();
		$domain = $uri->toString(array('host'));

		$testdomain = "";
		$testurl = "";
		for ($i=1;$i<=10;$i++){
			$testdomain = $compparams->get("googledomain".$i,"");
			if ($testdomain=="") break;
			$testurl = $compparams->get("googledomainurl".$i,"");			
			if ($domain == $testdomain){
				return $testurl;
			}
		}
		return $googleurl;
		
	}
	
	static function canUploadImages() {
		$params =& JComponentHelper::getParams('com_jevents');
		$authorisedonly = $params->get("authorisedonly",0);
		if (!$authorisedonly){
			$params =& JComponentHelper::getParams("com_jevlocations");
			$uploadImages = $params->get("upimageslevel",25);
			$juser =& JFactory::getUser();
			if ($juser->gid>=intval($uploadImages)){
				return true;
			}
		}
		else {
			$jevuser =& JEVHelper::getAuthorisedUser();
			if 	($jevuser && $jevuser->canuploadimages){
				return true;
			}
		}
		return false;
	}
	
	function processFileUpload($file){
		set_time_limit(1800);

		if (!array_key_exists("HTTP_REFERER",$_SERVER) || (strpos($_SERVER["HTTP_REFERER"],"http://".$_SERVER["HTTP_HOST"] )!==0 && strpos($_SERVER["HTTP_REFERER"],"https://".$_SERVER["HTTP_HOST"] )!==0)){
			die();
		}

		if (!isset($_FILES[$file]) || $_FILES[$file]['error']==UPLOAD_ERR_NO_FILE) {
			$this->error_goback(JText::_("Missing File"));
		}

		// this should be set in config
		$filesize = $_FILES[$file]["size"];
		$maxsize = $this->params->getValue("maxuploadfile",2000000);
		if ($_FILES[$file]["size"]>$maxsize){
			$this->error_goback(JText::sprintf("File Too Large",$filesize,$maxsize));
		}

		$this->securityCheck( $_FILES[$file]);

		$suffix = "pdf";
		if (!$this->checkFileType($_FILES[$file]["type"],$_FILES[$file]["name"],$suffix)){
			$this->error_goback(JText::_("Invalid file type"));
		}

		$ftmp = $_FILES[$file]['tmp_name'];
		$oname = $_FILES[$file]['name'];

		$fileName = $oname;

		$fileName = uniqid(null,true).".".$suffix;

		$folder		= JRequest::getVar( 'folder', '', '', 'path' );

		$filelocation = JEVP_MEDIA_BASE.DS.$folder;


		$fname = $filelocation.DS.$fileName;
		jimport("joomla.filesystem.file");
		if(!JFile::copy($ftmp, $fname)){
			if (!rename($ftmp, $fname)){
				$this->error_goback(JText::_("Could not save"));
			}
		}
		@chmod($fname,0644);

		return $fileName;
	}


	function processImageUpload($file){
		set_time_limit(1800);

		if (!array_key_exists("HTTP_REFERER",$_SERVER) || (strpos($_SERVER["HTTP_REFERER"],"http://".$_SERVER["HTTP_HOST"] )!==0 && strpos($_SERVER["HTTP_REFERER"],"https://".$_SERVER["HTTP_HOST"] )!==0)){
			die();
		}

		if (!isset($_FILES[$file]) || $_FILES[$file]['error']==UPLOAD_ERR_NO_FILE) {
			$this->error_goback(JText::_("Missing Image File"));
		}

		// this should be set in config
		$maxsize = $this->params->getValue("maxupload",2000000);
		$imagesize = $_FILES[$file]["size"];
		if ($_FILES[$file]["size"]>$maxsize){
			$this->error_goback(JText::sprintf("Image Too Large",$imagesize,$maxsize));
		}

		$this->securityCheck( $_FILES[$file]);

		$suffix = "jpg";
		if (!$this->checkImageType($_FILES[$file]["type"],$suffix)){
			$this->error_goback(JText::sprintf("Invalid TYPE",$_FILES[$file]["type"]));
		}
		$ftmp = $_FILES[$file]['tmp_name'];
		$oname = $_FILES[$file]['name'];

		$fileName = uniqid(null,true).".".$suffix;

		$folder = "jevents/jevlocations";

		$filelocation = JEVP_MEDIA_BASE.DS.$folder;
		
		$fname = $filelocation.DS.$fileName;
		jimport("joomla.filesystem.file");
		if(!JFile::copy($ftmp, $fname)){
			if (!rename($ftmp, $fname)){
				$this->error_goback(JText::_("could not save"));
			}
		}
		@chmod($fname,0644);

		// scale the image
		$imagew = $this->params->getValue("imagew");
		$imageh = $this->params->getValue("imageh");
		$no_thumbanil = $this->params->getValue("no_thumbanil");
		if ($imagew>0 && $imageh>0){
			$this->scaleImage($fname,$imagew,$imageh, $imagesize, $no_thumbanil,false);
		}

		// create the thumbnail
		$thumbw = $this->params->getValue("thumbw");
		$thumbh = $this->params->getValue("thumbh");
		$this->scaleImage($fname, $thumbw, $thumbh, $imagesize, $no_thumbanil,true);

		return $fileName;
	}

	function error_goback($msg){
	?>
	<html>
	<head>
	<script language="javascript" type="text/javascript">
	alert("<?php echo $msg;?>");
	history.go(-1);
		</script>
	</head>
	<body>
	</body>
	</html>
	<?php
	exit();
	}

	function checkImageType($type,&$suffix){
		static $allowedImageTypes = array("image/png","image/jpeg","image/pjpeg","image/gif");
		if (!in_array($type,$allowedImageTypes)){
			return false;
		}
		$suffix = str_replace("image/","",$type);
		return true;
	}

	function checkFileType($type,$filename,&$suffix){
		static $allowedfileExtensions = array("pdf","xls","xlsx","doc","docx","flv");
		if (strrpos($filename,".")>0){
			$suffix = strtolower(substr($filename,strrpos($filename,".")+1));
			if (in_array($suffix,$allowedfileExtensions)){
				return true;
			}
		}
		return false;
	}


	function securityCheck($file){
		if (!is_uploaded_file($file["tmp_name"])){
			//$this->error_goback("Problems uploading file <b>".$file['name']."</b>");
			// Failed for various reasons
			switch ($file["error"] ) {
				case UPLOAD_ERR_OK:
					break;
				case UPLOAD_ERR_INI_SIZE:
					$this->error_goback("The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.");
					break;
				case UPLOAD_ERR_FORM_SIZE:
					$this->error_goback("The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.");
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->error_goback( "The uploaded file was only partially uploaded.");
					break;
				case UPLOAD_ERR_NO_FILE:
					$this->error_goback("No file was uploaded.");
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->error_goback("Missing a temporary folder.");
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->error_goback("Failed to write file to disk");
					break;
				default:
					$this->error_goback("Unknown File Error");
			}
		}
	}

	function scaleImage($img, $maxwidth=150,$maxheight=150, $imagesize=10, $nothumbnail=1500000, $thumb=true){
		$info = getimagesize($img);
		if (!$info) {
			$this->error_goback(JText::_("problems creating thumbnail"));
			return;
		}
		$origw = $info[0];
		$origh = $info[1];
		// TODO thumbnail size in config
		// don't make thumbnail bigger than original!!
		if ($origw<=$maxwidth && $origh<=$maxheight){
			$thumbWidth = $origw;
			$thumbHeight = $origh;
		}
		else {
			$thumbWidth = $maxwidth;
			$thumbHeight = intval($origh * $thumbWidth/$origw);
			if ($thumbHeight>$maxheight){
				$thumbHeight = $maxheight;
				$thumbWidth = intval($origw * $thumbHeight/$origh);
			}
		}

		$imgtypes = array( 1 => 'GIF', 2 => 'JPG', 3 => 'PNG', 4 => 'SWF', 5 => 'PSD', 6 => 'BMP', 7 => 'TIFF', 8 => 'TIFF', 9 => 'JPC', 10 => 'JP2', 11 => 'JPX', 12 => 'JB2', 13 => 'SWC', 14 => 'IFF', 15 => 'WBMP', 16 => 'XBM');

		// GD can only handle JPG & PNG images
		if ($info[2] >=4) {
			$this->error_goback(JText::_("ERROR FILE TYPE"));
		}

		// Create the thumbnail
		if (!function_exists('imagecreatefromjpeg')) {
			$this->error_goback(JText::_("problems creating thumbnail")." 2");
			return false;
		}
		if (!function_exists('imagecreatetruecolor')) {
			$this->error_goback(JText::_("problems creating thumbnail")." 3");
			return false;
		}

		if ($imagesize>$nothumbnail){
			$src_img = imagecreatefromjpeg(dirname(__FILE__)."/success.jpg");
			$origh=100;
			$origw=149;
			$thumbHeight=100;
			$thumbWidth=150;
		}
		else {
			if ($info[2] == 2) {
				$src_img = imagecreatefromjpeg($img);
			}
			else if ($info[2] == 3){
				$src_img = imagecreatefrompng($img);
			}
			else {
				$src_img = imagecreatefromgif($img);
			}
		}

		if (!$src_img){
			$this->error_goback(JText::_("problems creating thumbnail")." 4");
			return false;
		}

		// TODO set thumbnail directories in config
		if ($thumb){

			$dst_img = imagecreatetruecolor($thumbWidth, $thumbHeight);

			if (strpos(basename($img),".png")>0){

				// Turn off alpha blending and set alpha flag
				imagealphablending($dst_img, false);
				imagesavealpha($dst_img, true);
				
				$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
				imagefilledrectangle($dst_img, 0, 0, $thumbWidth, $thumbHeight, $transparent);
			}

			$res = imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origw, $origh);
			//$res = imagecopymerge($dst_img, $src_img, 0, 0, 0, 0, $thumbWidth, $thumbHeight,100);
			
			$thumbdir = dirname($img).DIRECTORY_SEPARATOR."thumbnails";
			$dest_file = $thumbdir.DIRECTORY_SEPARATOR."thumb_".basename($img);

			$this->ensureDirExists($thumbdir);

			$tmp = tempnam($thumbdir,"img");

			if (strpos(basename($img),".png")>0){
				imagepng($dst_img, $tmp);
			}
			else {
				// TODO set quality for image save in config
				imagejpeg($dst_img, $tmp,80);
			}

			if (!JFile::copy($tmp,$dest_file)){
				var_dump(JError::getErrors());
				return false;
			}
			unlink($tmp);

		}
		else {
			$thumbdir = dirname($img);
			$dest_file = $thumbdir.DIRECTORY_SEPARATOR.basename($img);

			$dst_img = imagecreatetruecolor($thumbWidth, $thumbHeight);
			
			if (strpos(basename($img),".png")>0){

				// Turn off alpha blending and set alpha flag
				imagealphablending($dst_img, false);
				imagesavealpha($dst_img, true);
				
				$transparent = imagecolorallocatealpha($dst_img, 255, 255, 255, 127);
				imagefilledrectangle($dst_img, 0, 0, $thumbWidth, $thumbHeight, $transparent);
			}

			imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origw, $origh);
			
			$tmp = tempnam($thumbdir,"img");
			if (strpos(basename($img),".png")>0){
				imagepng($dst_img, $tmp);
			}
			else {
				// TODO set quality for image save in config
				imagejpeg($dst_img, $tmp,80);
			}

			if (!JFile::copy($tmp,$dest_file)){
				var_dump(JError::getErrors());
			}
			unlink($tmp);
		}
		// remove copies in memory
		imagedestroy($src_img);
		imagedestroy($dst_img);

		// We check that the image is valid
		$imginfo = getimagesize($dest_file);
		if ($imginfo == null){
			$this->error_goback(JText::_("problems creating thumbnail")." 5");
			return false;
		} else {
			@chmod($dest_file,0644);
			return true;
		}
	}

	function ensureDirExists($targetDir){
		clearstatcache();
		if (!JFolder::exists($targetDir )) {
			if (!JFolder::create($targetDir,0777)) {
				$this->error_goback( "can't create directory $targetDir");
			}
		}
	}
	

}
