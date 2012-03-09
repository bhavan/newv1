<?php
/**
* @version 1.0.4
* @package RSform! 1.0.4
* @copyright (C) 2007 www.pimpmyjoomla.com
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


$fid 	= intval( $params->def( 'form_id', 0 ) );

if(!function_exists('showForm')){
	require_once( $mainframe->getPath( 'front_html','com_forme' ) );


	//check language
	//first check global joomfish
	$elpath = JPATH_SITE.'/components/com_forme';
	$check = false;
	if(isset($_COOKIE['mbfcookie']['lang'])) $check = $_COOKIE['mbfcookie']['lang'];
	if(isset($_REQUEST['lang'])) $check = mosGetParam($_REQUEST,'lang',false);
	if($check){
		require_once($elpath.'/languages/'.$check.'.php');
	}else{
		require_once($elpath.'/languages/en.php');
	}

	function showForm($option, $fid){
		global $mainframe, $limitstart, $acl, $processform;

		$database =& JFactory::getDBO();
		$my = & JFactory::getUser();

		$elpath = JPATH_SITE.'/components/com_forme';

		require_once( $mainframe->getPath( 'front_html','com_forme' ) );

		if(!$fid){
			//get first cid
			$database->setQuery("SELECT id FROM #__forme_forms WHERE published = 1 LIMIT 1");
			$fid = (int)$database->loadResult();
		}

		//check language
		//first check global joomfish
		$check = false;
		if(isset($_COOKIE['mbfcookie']['lang'])) $check = $_COOKIE['mbfcookie']['lang'];
		if(isset($_REQUEST['lang'])) $check = mosGetParam($_REQUEST,'lang',false);
		if($check){

			$database->setQuery("SELECT * FROM #__forme_forms WHERE id = '$fid'");
			$oldform = $database->loadObject();

			//check if we find something similar
			$database->setQuery("SELECT id FROM #__forme_forms WHERE lang='$check' AND name='$oldform->name' ");
			$newfid = $database->loadResult();
			if($newfid) $fid = $newfid;
		}

		$query = "SELECT * FROM #__forme_forms WHERE id = '{$fid}' AND published = '1'";
		$database->setQuery($query);

		$form = $database->loadObject();

		//load fields
		$query = "SELECT * FROM #__forme_fields WHERE form_id = '{$fid}' AND published = '1' ORDER BY ordering";
		$database->setQuery($query);

		$fields = $database->loadObjectList();

		if(!$form->published) mosRedirect(JURI::base(),_NOT_EXIST);

		//Output
		global $database, $Itemid, $mainframe, $params, $hide_js, $pop, $formeConfig;

		$html = '';
		if(isset($form->id)){

			//mosCommonHTML::loadCalendar();
			eval($form->script_display);

	    	//if we have upload file fields, add enctype
	    	$enctype='';
		    foreach($fields as $field){
		    	if($field->inputtype=='file upload') $enctype = ' enctype="multipart/form-data"';
		    }

			//load calendar if calendar field exists
			$calexists = false;
			foreach($fields as $field){
				if($field->inputtype=='calendar') $calexists = true;
			}

			//parse field template
			$formfields = '';
			foreach($fields as $field){
				if($form->fieldstyle=='') $form->fieldstyle = _FORME_BACKEND_EDITFORMS_FIELDSTYLE_DEFAULT;
				if($field->fieldstyle=='') $field->fieldstyle = $form->fieldstyle;
				$formfields .= forme_HTML::parseFields($field);
			}

			if($calexists){

				$check = false;
				if(isset($_COOKIE['mbfcookie']['lang'])) $check = $_COOKIE['mbfcookie']['lang'];
				if(isset($_REQUEST['lang'])) $check = mosGetParam($_REQUEST,'lang',false);
				if($check){
					if(file_exists(JPATH_SITE.'/components/com_forme/calendar/initcal-'.$check.'.php'))
					require_once(JPATH_SITE.'/components/com_forme/calendar/initcal-'.$check.'.php');
					else require_once(JPATH_SITE.'/components/com_forme/calendar/initcal.php');
				}
				else require_once(JPATH_SITE.'/components/com_forme/calendar/initcal.php');
				$html .='
					<script language="javascript">
						function init() {';

							foreach($fields as $field){
								if($field->inputtype=='calendar'){
							$html.='

								function handleSelect'.$field->name.'(type,args,obj) {
									var dates = args[0];
									var date = dates[0];
									var year = date[0], month = date[1], day = date[2];
									var txtDate = document.getElementById("txt'.$field->name.'");
									txtDate.value = month + "/" + day + "/" + year;
								}


								YAHOO.example.calendar.'.$field->name.' = new YAHOO.widget.Calendar("'.$field->name.'","'.$field->name.'Container");
								YAHOO.example.calendar.'.$field->name.'.selectEvent.subscribe(handleSelect'.$field->name.', YAHOO.example.calendar.'.$field->name.', true);


								var txt'.$field->name.' = document.getElementById("txt'.$field->name.'");

								if (txt'.$field->name.'.value != "") {
									YAHOO.example.calendar.'.$field->name.'.select(txt'.$field->name.'.value);
								}

						    	YAHOO.example.calendar.'.$field->name.'.render();';

								}
							}
						    $html .='
						}

						YAHOO.util.Event.addListener(window, "load", init);
					</script>';

			}


			$action = 'index.php?option=com_forme&fid='.$form->id.'&Itemid='.$Itemid;

			//parse form template
			if($form->formstyle == '') $form->formstyle = _FORME_BACKEND_EDITFORMS_STYLE_DEFAULT;
			$form->formstyle = str_replace('{formtitle}',$form->title,$form->formstyle);
			$form->formstyle = str_replace('{formname}',$form->name,$form->formstyle);
			$form->formstyle = str_replace('{enctype}',$enctype,$form->formstyle);
			$form->formstyle = str_replace('{action}',$action,$form->formstyle);

			$form->formstyle = str_replace('{formfields}',$formfields,$form->formstyle);

			$html .= $form->formstyle;

		}
	 	return $html;
	}
}
echo showForm('com_forme',$fid);

?>