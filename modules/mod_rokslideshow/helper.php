<?php
/**
 * RokSlideshow Module
 *
 * @package		Joomla
 * @subpackage	RokSlideshow Module
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
jimport('joomla.utilities.date');

class modRokSlideshowHelper
{
	
	function loadScripts($params, $imagePath, $images) {
		$doc = &JFactory::getDocument();
		JHTML::_('behavior.mootools');
		$doc->addScript('modules/mod_rokslideshow/tmpl/rokslideshow.js');

		$showCaption 	= $params->get( 'showCaption', 1 );
		$showTitleCaption 	= $params->get( 'showTitleCaption', 1 );
		$externals = $params->get('externals', 0);
		$heightCaption = $params->get('heightCaption', 45);
		$width = $params->get( 'width', 400 );
		$height = $params->get( 'height', 300 );
		$altTag = $params->get( 'altTag', 'RokSlideshow - http://www.rocketwerx.com' );
		$imageDuration = $params->get( 'imageDuration', 9000 );
		$transDuration = $params->get( 'transDuration', 2000);
		$transType = $params->get( 'transType', 'combo');
		$transition = $params->get( 'transition', 'Expo.easeOut');
		$pan = $params->get( 'pan', 50);
		$zoom = $params->get( 'zoom', 50);
		$loadingDiv = $params->get( 'loadingDiv', 1);
		$imageResize = $params->get( 'imageResize', 1);
		$titleSize = $params->get( 'titleSize', '13px');
		$titleColor = $params->get( 'titleColor', '#fff');
		$descSize = $params->get( 'descSize', '11px');
		$descColor = $params->get( 'descColor', '#ccc');
		
		$imgPush = "window.addEvent('load', function() {\n";
		$imgPush .= "	var imgs = [];\n";
		
		foreach($images as $img) {
			$info = modRokSlideshowHelper::getInfo($imagePath, $img);
			$imgPush .= "	imgs.push({file: '$img', ";
			if ($showCaption == 1) {
				$imgPush .= "title: '".trim($info[0])."', desc: '".trim($info[2])."', url: '".trim($info[1])."'";
			} else {
				$imgPush .= "title: '', desc: '', url: ''";
			}
			$imgPush .= "});\n";
		}
		
		$imgPush .= "	var myshow = new Slideshow('slideshow', { 
		type: '$transType',
		externals: $externals,
		showTitleCaption: $showTitleCaption,
		captionHeight: $heightCaption,
		width: $width, 
		height: $height, 
		pan: $pan,
		zoom: $zoom,
		loadingDiv: $loadingDiv,
		resize: ". (($imageResize == 1) ? 'true' : 'false').",
		duration: [$transDuration, $imageDuration],
		transition: Fx.Transitions.$transition,
		images: imgs, 
		path: '".JURI::base().$imagePath."'
	});

	myshow.caps.h2.setStyles({color: '$titleColor', fontSize: '$titleSize'});
	myshow.caps.p.setStyles({color: '$descColor', fontSize: '$descSize'});
});";
					
		$doc->addScriptDeclaration($imgPush);
	}
	
	//helper functions
	function imageList ($directory, $sortcriteria, $sortorder) {
	    $results = array();
	    $handler = opendir($directory);
			$i = 0;
	    while ($file = readdir($handler)) {
	        if ($file != '.' && $file != '..' && modRokSlideshowHelper::isImage($file)) {
						$results[$i][0] = $file;
						$results[$i][1] = filemtime($directory . "/" .$file);
						$i++;
					}
	    }
	    closedir($handler);

			//these lines sort the contents of the directory by the date
			// Obtain a list of columns

			foreach($results as $res) {
				if ($sortcriteria == 0 ) $sortAux[] = $res[0];
				else $sortAux[] = $res[1];
			}

			if ($sortorder == 0) {
				array_multisort($sortAux, SORT_ASC, $results);
			} elseif ($sortorder == 2) {
				srand((float)microtime() * 1000000);
				shuffle($results);
			} else {
				array_multisort($sortAux, SORT_DESC, $results);
			}

			foreach($results as $res) {
				$sorted_results[] = $res[0];
			}

	    return $sorted_results;
	}

	function getInfo($imagePath, $file) {
			global $iso_client_lang;

			$langext = "";
			$fileext= ".txt";

			if (isset($iso_client_lang) && strlen($iso_client_lang)>1) $langext = "." . $iso_client_lang;

			$file_noext = substr($file, 0, strrpos($file,"."));
			$info = array();

			$infofile = $imagePath . $file_noext . $langext . $fileext;

			if (!file_exists($infofile)) $infofile = $imagePath . $file_noext . $fileext;
			if (file_exists($infofile)) {
				$imginfo = file ($infofile);
				foreach ($imginfo as $line) {
					$info[] = addslashes($line);
				}
				return $info;
			}
			return array('','#','');
	}

	function isImage($file) {
		$imagetypes = array(".jpg", ".jpeg", ".gif", ".png");
		$extension = substr($file,strrpos($file,"."));
		if (in_array($extension, $imagetypes)) return true;
		else return false;
	}

	function cleanDir($dir) {
		if (substr($dir, -1, 1) == '/')
			return $dir;
		else
			return $dir . "/";
	}

}
