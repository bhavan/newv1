<?php
/**
* @version 1.0.4
* @package RSform! Into Content 1.0.4
* @copyright (C) 2007 www.rsjoomla.com
* @license Commercial License, http://www.rsjoomla.com/license/forme.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


@session_start();

$mainframe->registerEvent( 'onPrepareContent', 'plgMosForme' );
/**
* Mambot that loads module positions within content
*/
function plgMosForme( &$row, &$params, $page=0 ) {

	$database =& JFactory::getDBO();


	$plugin =& JPluginHelper::getPlugin('content', 'mosforme');

	// simple performance check to determine whether bot should process further
	if ( strpos( $row->text, 'mosforme' ) === false ) {
		return true;
	}

 	// expression to search for
 	$regex = '/{mosforme\s*.*?}/i';


	// find all instances of mambot and put in $matches
	preg_match_all( $regex, $row->text, $matches );

	// Number of mambots
 	$count = count( $matches[0] );

 	// mambot only processes if there are any instances of the mambot in the text
 	if ( $count ) {

 		processForme( $row, $matches, $count, $regex );
	}
}

function processForme ( &$row, &$matches, $count, $regex ) {
	$my = & JFactory::getUser();
	$CONFIG = new JConfig();
	$database =& JFactory::getDBO();


		$func 			= JRequest::getVar( 'func' );
		$did			= JRequest::getVar('did','');
	for ( $i=0; $i < $count; $i++ ) {
		$load = str_replace( 'mosforme', '', $matches[0][$i] );
 		$load = str_replace( '{', '', $load );
 		$load = str_replace( '}', '', $load );
 		$load = trim( $load );




		switch ($func) {
			case 'thankyou':
				//thankyou($option, $did);
				$row->text 	= thankyou('com_forme', $did);//str_replace($matches[0][$i], thankyou('com_forme', $did), $row->text);
			break;
			default:
				$row->text 	= str_replace($matches[0][$i], showForm('com_forme',$load), $row->text);
			break;
		}




	}



  	// removes tags without matching module positions
	$row->text = preg_replace( $regex, '', $row->text );
}







if(!function_exists('showForm')){
	require_once( dirname(__FILE__)  .'/../../plugins/system/legacy/functions.php' );

	function processForm($fid, $processform){
		global  $Itemid, $mainframe;

		if(!$Itemid) $Itemid = 999999;
		$my = & JFactory::getUser();
		$CONFIG = new JConfig();
		$database =& JFactory::getDBO();

		require_once( $mainframe->getPath( 'class','com_forme' ) );

		$row = new forme_data($database);
		$row->form_id = $fid;
		$data_id = 0;

		$form = new forme_forms($database);
		$form->load($fid);



		eval($form->script_process);

		if(!empty($processform)){
			$errors = false;
			$form_data = JRequest::getVar('form',array(),'POST');
			if(!empty($form_data)){
				foreach($form_data as $key=>$value){
					$form_data[$key] = RemoveXSS($form_data[$key]);
				}
			}

			$row->uip = $_SERVER['REMOTE_ADDR'];
			$row->date_added = date('Y-m-d H:i:s');
			$_SESSION['formmsg'] = array();

			//check captcha if any
			$database->setQuery("SELECT * FROM #__forme_fields WHERE form_id = '$fid' AND published = 1");
			$fields = $database->loadObjectList();


			//load input data
			foreach($fields as $field){
				if(isset($form_data[$field->name])){
					$_SESSION['formdata'][$field->name] = $form_data[$field->name];
				}else{
					$_SESSION['formdata'][$field->name] = array();
				}
			}

			foreach($fields as $i=>$field){
				//check captcha
				if($field->inputtype == 'captcha'){
					//check session
					if(isset($_SESSION['CAPTCHA'])){
						if(isset($form_data[$field->name])){
							if(strtoupper($form_data[$field->name])!=$_SESSION['CAPTCHA']){
								$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ? _FORME_FRONTEND_REGISTRA_CAPTCHA : $field->validation_message;
							}
						}
						else
							$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ? _FORME_FRONTEND_REGISTRA_CAPTCHA : $field->validation_message;
					}else{
						$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ? _FORME_FRONTEND_REGISTRA_CAPTCHA : $field->validation_message;
					}
				}


				//check mandatory
				if($field->validation_rule == 'mandatory'){
					if($field->inputtype!='file upload'){
						if(isset($form_data[$field->name])){
							if(is_array($form_data[$field->name])){
								if(empty($form_data[$field->name])||(count($form_data[$field->name])==1&&$form_data[$field->name][0]=='')) $_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_CANNOT_EMPTY,$field->title) : $field->validation_message;
							}else{
								if($form_data[$field->name]=='')  $_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_CANNOT_EMPTY,$field->title) : $field->validation_message;
							}
						}else{
							$_SESSION['formmsg'][$field->name][] =  $_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_CANNOT_EMPTY,$field->title) : $field->validation_message;
						}
					}else{
						$field_exists = false;
						foreach($_FILES['form']['name'] as $field_name=>$field_value){
							if($field_name==$field->name){
								if($_FILES['form']['tmp_name'][$field_name]!='')	$field_exists = true;
							}
						}
						if(!$field_exists){
							$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_FILE_CANNOT_EMPTY) : $field->validation_message;
						}
					}

				}
				//check alphanum
				if($field->validation_rule == 'alphanum'){
					if(eregi('[^a-zA-Z0-9 ]', $form_data[$field->name] )|| $form_data[$field->name] == ''){
						$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_ALPHANUMERIC,$field->title) : $field->validation_message;
					}
				}

				//check alpha
				if($field->validation_rule == 'alpha'){
					if(eregi('[^a-zA-Z ]', $form_data[$field->name] )|| $form_data[$field->name] == ''){
						$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_ALPHA,$field->title) : $field->validation_message;
					}
				}

				//check email
				if($field->validation_rule == 'email'){
					if(!eregi ("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,6}$", stripslashes(trim($form_data[$field->name]))) || $form_data[$field->name] == ''){
						$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  _FORME_FRONTEND_REGISTRA_EMAIL : $field->validation_message;
					}
				}

				//check number
				if($field->validation_rule == 'number'){
					if(!is_numeric($form_data[$field->name] )|| $form_data[$field->name] == ''){
						$_SESSION['formmsg'][$field->name][] = ($field->validation_message == '') ?  sprintf(_FORME_FRONTEND_REGISTRA_NUMERIC,$field->title) : $field->validation_message;
					}
				}

				//check file type compatibility
				if($field->inputtype== 'file upload'){
					foreach($_FILES['form']['name'] as $field_name=>$field_value){
						if($field_name == $field->name){
							$field_rules = explode(',',$field->default_value);
							if( $field_rules[0] != '' || $field->default_value!=''){
								$type_match = false;
								foreach($field_rules as $rule){
									//check for size
									$size = explode('/',$rule);
									if($size[0]=='size'){
										if(!isset($size[1])) $size[1] = 0;
										if($size[1]){
											if($_FILES['form']['size'][$field->name] > (int)$size[1]*1024){
												$_SESSION['formmsg'][$field->name][] = sprintf(_FORME_FRONTEND_REGISTRA_SIZE,$size[1]);
											}
										}
									}

									//check for file type
									if($rule==$_FILES['form']['type'][$field->name]){
										$type_match = true;
									}
								}
								if($_FILES['form']['type'][$field->name]=='') $type_match = true;
								if(!$type_match){
									$_SESSION['formmsg'][$field->name][] = _FORME_FRONTEND_REGISTRA_NOT_ALLOWED;
								}
							}
						}
					}
				}

			}
			if(!empty($_SESSION['formmsg'])){
				$mainframe->redirect(str_replace( '&amp;', '&', $_SERVER['REQUEST_URI']));
			}


			//build params
			$params_field = '';
			foreach($form_data as $key=>$value){
				if(is_array($value)) $value = implode(',',$value);
				$params_field .= $key.'='.$value."||\n";
			}

			//files
			foreach($fields as $field){
				if($field->inputtype=='file upload'){

						$target_file = time().'_'.$_FILES['form']['name'][$field->name];

						if(!move_uploaded_file($_FILES['form']['tmp_name'][$field->name],JPATH_SITE.'/components/com_forme/uploads/'.$target_file)){

						}else{
							@chmod(JPATH_SITE.'/components/com_forme/uploads/'.$target_file,0755);
							if(!isset($array_files)) $array_files = array();
							$array_files[] = JPATH_SITE.'/components/com_forme/uploads/'.$target_file;
							$params_field .= $field->name . '=' . $target_file ."||\n";
						}
				}
			}




			// bind it to the table
			if (!$row -> bind($form_data)) {
				echo "<script> alert('"
				.$row -> getError()
					."'); window.history.go(-1); </script>\n";
				exit();
			}else{
				$row->params = $params_field;
			}
			if($my->id) $row->uid = $my->id;

			// store it in the db
			if (!$row -> store()) {
				echo "<script> alert('"
					.$row -> getError()
					."'); window.history.go(-1); </script>\n";
				exit();
			}else{
				//$data_id = mysql_insert_id();
				$data_id = $row->id;
				if($form->emailto!=''&&$form->email!=''){
					$emailto = explode(',',str_replace(' ','',$form->emailto));

					$fields = populateGlobal($fields);

					$params = prepareParams($data_id);
					foreach($fields as $field){
						if(!isset($params[$field->name])) $params[$field->name] = '';
						$form->email = str_replace('{'.$field->name.'}',$params[$field->name],$form->email);
						$form->emailsubject = str_replace('{'.$field->name.'}',$params[$field->name],$form->emailsubject);
						$form->emailfrom = str_replace('{'.$field->name.'}',$params[$field->name],$form->emailfrom);
						$form->emailfromname = str_replace('{'.$field->name.'}',$params[$field->name],$form->emailfromname);
						foreach($emailto as $i=>$to){
							$emailto[$i] = str_replace('{'.$field->name.'}',$params[$field->name],$emailto[$i]);
						}
					}

					if($form->emailfrom=='')$form->emailfrom = $CONFIG->mailfrom;
					if($form->emailfromname=='')$form->emailfromname = $CONFIG->sitename;


					foreach($emailto as $to){
						JUtility::sendMail($form->emailfrom,$form->emailfromname,$to,$form->emailsubject,$form->email,$form->emailmode,null,null,$array_files);
					}
				}
			}

			//check if there is a thank you message
			if(strlen($form->thankyou)!=0){
				if(isset($_SESSION['formdata'])){
					unset($_SESSION['formdata']);
				}
				//$mainframe->redirect( str_replace( '&amp;', '&', "index.php?option=com_forme&func=thankyou&did=" . md5( $data_id.$row->date_added )."&Itemid=$Itemid"));
				if(stristr($_SERVER['REQUEST_URI'],'?')) $sign = '&';
				else $sign = '?';
				$mainframe->redirect( $_SERVER['REQUEST_URI'] . $sign . "func=thankyou&did=" . md5( $data_id.$row->date_added ));
			}else {
				if(isset($_SESSION['formdata'])){
					unset($_SESSION['formdata']);
				}
				//if there is a return url
				if($form->return_url!=''){

					$params = prepareParams($data_id);
					$fields = populateGlobal($fields);
					foreach($fields as $field){
						if(!isset($params[$field->name])) $params[$field->name] = '';
						$form->return_url = str_replace('{'.$field->name.'}',$params[$field->name],$form->return_url);
					}
					$mainframe->redirect(str_replace( '&amp;', '&', $form->return_url), _FORME_FRONTEND_REGISTRA_SUCCESS." ");
				}else{
					if(isset($_SESSION['formdata'])){
						unset($_SESSION['formdata']);
					}
					//$mainframe->redirect(str_replace( '&amp;', '&', sefRelToAbs("index.php?option=com_forme&Itemid=$Itemid&func=details&fid=$fid")), _FORME_FRONTEND_REGISTRA_SUCCESS." ");
					if(stristr($_SERVER['REQUEST_URI'],'?')) $sign = '&';
					else $sign = '?';
					$mainframe->redirect( $_SERVER['REQUEST_URI'],_FORME_FRONTEND_REGISTRA_SUCCESS);
				}
			}
		}

	}

	function showForm($option, $fid){
		global $mainframe, $limitstart, $processform;

		$my = & JFactory::getUser();
		$CONFIG = new JConfig();
		$database =& JFactory::getDBO();
		$elpath = JPATH_SITE.'/components/com_forme';

		$processform	= JRequest::getVar( 'form', array(), 'POST');
		if(!empty($processform)){
			foreach($processform as $key=>$value){
				$processform[$key] = RemoveXSS($processform[$key]);
			}
		}
		$check = false;
		if(isset($_COOKIE['mbfcookie']['lang'])) $check = $_COOKIE['mbfcookie']['lang'];
		if(isset($_COOKIE['jfcookie']['lang'])) $check = $_COOKIE['jfcookie']['lang'];
		if(isset($_REQUEST['lang'])) $check = JRequest::getVar('lang',false);
		if($check && file_exists($elpath.'/languages/'.$check.'.php')){
			require_once($elpath.'/languages/'.$check.'.php');
		}else{
			require_once($elpath.'/languages/en.php');
		}


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
		if(isset($_COOKIE['jfcookie']['lang'])) $check = $_COOKIE['jfcookie']['lang'];
		if(isset($_REQUEST['lang'])) $check = JRequest::getVar('lang',false);
		if($check){
			$database->setQuery("SELECT name FROM #__forme_forms WHERE id = '$fid'");
			$old_name = $database->loadResult();

			//check if we find something similar
			$database->setQuery("SELECT id FROM #__forme_forms WHERE lang='$check' AND name='$old_name' ");
			$newfid = $database->loadResult();
			if($newfid) $fid = $newfid;
		}


		processForm($fid, $processform);



		$query = "SELECT * FROM #__forme_forms WHERE id = '{$fid}' AND published = '1'";
		$database->setQuery($query);

		$form = $database->loadObject();

		//load fields
		$query = "SELECT * FROM #__forme_fields WHERE form_id = '{$fid}' AND published = '1' ORDER BY ordering";
		$database->setQuery($query);

		$fields = $database->loadObjectList();

		if(!$form->published) $mainframe->redirect(JRoute::_(JURI :: base(),_NOT_EXIST));

		//Output
		global $Itemid, $mainframe, $params, $hide_js, $pop, $formeConfig;

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
				if(isset($_COOKIE['jfcookie']['lang'])) $check = $_COOKIE['jfcookie']['lang'];
				if(isset($_REQUEST['lang'])) $check = JRequest::getVar('lang',false);
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


			//$action = sefRelToAbs('index.php?option=com_forme&fid='.$form->id.'&Itemid='.$Itemid);
			$action = '';

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

	function populateGlobal($fields){
		$fields[]->name = 'jos_sitename';
		$fields[]->name = 'jos_siteurl';
		$fields[]->name = 'jos_userip';
		$fields[]->name = 'jos_user_id';
		$fields[]->name = 'jos_username';
		$fields[]->name = 'jos_email';

		return $fields;
	}

	function prepareParams($did){
		$my = & JFactory::getUser();
		$CONFIG = new JConfig();
		$database =& JFactory::getDBO();



		$database->setQuery("SELECT * FROM #__forme_data WHERE id = '$did'");
		$data_row = $database->loadObject();
		$params = $data_row->params;

		$database->setQuery("SELECT * FROM #__users WHERE id = '$data_row->uid'");
		$user = $database->loadObject();


		$result['jos_sitename'] = $CONFIG->sitename;
		$result['jos_siteurl'] = JURI :: base();
		$result['jos_userip'] = $data_row->uip;
		$result['jos_user_id'] = $data_row->uid;
		$result['jos_username'] = (isset($user->username)) ? $user->username : 0;
		$result['jos_email'] = (isset($user->email)) ? $user->email : 0;

		$result_explode = explode("||\n",$params);
		foreach($result_explode as $param_row){
			$param_row = explode('=',$param_row,2);
			if(isset($param_row[1])){
				$result[$param_row[0]] = $param_row[1];
			}else{
				$result[$param_row[0]] = '';
			}
		}
		return $result;
	}

	function RemoveXSS($val) {
	   // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
	   // this prevents some character re-spacing such as <java\0script>
	   // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
	   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);

	   // straight replacements, the user should never need these since they're normal characters
	   // this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
	      // ;? matches the ;, which is optional
	      // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

	      // &#x0040 @ search for the hex values
	      $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
	      // &#00064 @ 0{0,7} matches '0' zero to seven times
	      $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
	   }

	   // now the only remaining whitespace attacks are \t, \n, and \r
	   $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
	   $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
	   $ra = array_merge($ra1, $ra2);

	   $found = true; // keep replacing as long as the previous round replaced something
	   while ($found == true) {
	      $val_before = $val;
	      for ($i = 0; $i < sizeof($ra); $i++) {
	         $pattern = '/';
	         for ($j = 0; $j < strlen($ra[$i]); $j++) {
	            if ($j > 0) {
	               $pattern .= '(';
	               $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
	               $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
	               $pattern .= ')?';
	            }
	            $pattern .= $ra[$i][$j];
	         }
	         $pattern .= '/i';
	         $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
	         $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
	         if ($val_before == $val) {
	            // no replacements were made, so exit the loop
	            $found = false;
	         }
	      }
	   }
	   return $val;
	}

	function thankyou($option, $did){
		global $mainframe, $limitstart, $Itemid;

		$result = '';

		$elpath = JPATH_SITE.'/components/com_forme';
		$check = false;
		if(isset($_COOKIE['mbfcookie']['lang'])) $check = $_COOKIE['mbfcookie']['lang'];
		if(isset($_COOKIE['jfcookie']['lang'])) $check = $_COOKIE['jfcookie']['lang'];
		if(isset($_REQUEST['lang'])) $check = JRequest::getVar('lang',false);
		if($check){
			require_once($elpath.'/languages/'.$check.'.php');
		}else{
			require_once($elpath.'/languages/en.php');
		}

		require_once( $mainframe->getPath( 'front_html','com_forme' ) );
		$my = & JFactory::getUser();
		$CONFIG = new JConfig();
		$database =& JFactory::getDBO();

		//get the submission & form id
		$database->setQuery("SELECT * FROM #__forme_data WHERE MD5(CONCAT(id,date_added)) = '".$did."'");
		$formdata = $database->loadOBject();
		if(isset($formdata->form_id)){
			//get form_id
			//$database->setQuery("SELECT * FROM #__forme_data WHERE id = '$did'");
			//$formdata = $database->loadObject();

			//check if form has a thank you message
			$database->setQuery("SELECT * FROM #__forme_forms WHERE id = '$formdata->form_id'");
			$form = $database->loadObject();

			$params = prepareParams($formdata->id);

			//load fields
			$database->setQuery("SELECT * FROM #__forme_fields WHERE form_id = '$formdata->form_id' AND published = 1");
			$fields = $database->loadObjectList();

			$fields = populateGlobal($fields);

			foreach($fields as $field){
				if(!isset($params[$field->name])) $params[$field->name] = '';
				$form->thankyou = str_replace('{'.$field->name.'}',$params[$field->name],$form->thankyou);
				$form->return_url = str_replace('{'.$field->name.'}',$params[$field->name],$form->return_url);

			}
				if(stristr($_SERVER['REQUEST_URI'],'?')) $sign = '&';
				else $sign = '?';

			if($form->thankyou!='') {
				$return_url = ($form->return_url == '') ?  $_SERVER['HTTP_REFERER'] : $form->return_url;
				$result .= '<div class="thankyou">'.stripslashes($form->thankyou).'</div>';//
				$result .= '<input type="button" name="ok" value="'._FORME_FRONTEND_THANKYOU_BUTTON.'" onclick="window.location=\''.$return_url.'\';"/>';
				return $result;
			}else{
				//if there is a return url
				if($form->return_url!=''){
					$mainframe->redirect($form->return_url, _FORME_FRONTEND_REGISTRA_SUCCESS." ");
				}else{
					$mainframe->redirect(str_replace( '&amp;', '&', sefRelToAbs("index.php?option=com_forme&Itemid=$Itemid")), _FORME_FRONTEND_REGISTRA_SUCCESS." ");
				}
			}
		}else{
			$mainframe->redirect(str_replace( '&amp;', '&', sefRelToAbs("index.php?option=com_forme&Itemid=$Itemid")), _FORME_FRONTEND_REGISTRA_SUCCESS." ");
		}
	}


}

?>