<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 */

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ();

class JevCfParameter extends JParameter {

	private $event;
	private $filterelements;

	function __construct($data, $path = '',  $event) {
		$this->event = $event;
		$params = "";
		foreach ($data as $field) {
			// must make sure we preserver the carriage returns in text areas
			if (is_object($field)) $params.=$field->name."=".str_replace("\n",'\n',$field->value)."\n";
			else if (is_array($field)) {
				$params.=$field["name"]."=".str_replace("\n",'\n',$field["value"])."\n";
			}
		}
		$filterelements = array();
		return parent::__construct($params, $path);
	}

	public $jevparams = array ();
	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 * @since	1.5
	 */
	public function render($name = 'custom_', $group = '_default', &$customfields) {
		if (! isset ( $this->_xml [$group] )) {
			return false;
		}

		// Get all the categories and their parentage
		$db = JFactory::getDBO();
		$db->setQuery("SELECT id, parent_id from #__categories where section='com_jevents' and published=1");
		$catlist = $db->loadObjectList("id");

		$cats = array();
		foreach ($catlist as $cat){
			// extract the complete ancestry
			if (!array_key_exists($cat->id, $cats)){
				$cats[$cat->id]=array();
				$cats[$cat->id][]=$cat->id;
				$parent = ($cat->parent_id>0 && array_key_exists($cat->parent_id,$catlist))?$catlist[$cat->parent_id]:false;
				while($parent){
					$cats[$cat->id][]=$parent->id;
					$parent = ($parent->parent_id>0 && array_key_exists($parent->parent_id,$catlist))?$catlist[$parent->parent_id]:false;
				}
			}
		}
		// Must set this up for empty category too
		$cats[0]=array();
		$cats[][]=0;
		
		$cats = json_encode($cats);

		// setup required fields script
		$doc = JFactory::getDocument ();
		$script = <<<SCRIPT
var JevrRequiredFields = {
	fields: new Array(),
	verify:function (form){
		var messages =  new Array();
		valid = true;
		JevrRequiredFields.fields.each(function (item,i) {
			name = item.name;
			var matches = new Array();
			\$A(form.elements).each (function (testitem,testi) {
				if(testitem.name == name){
					matches.push(testitem);					
				};
			});
			var value = "";
			if(matches.length==1){
				value = matches[0].value;
			}
			// A set of radio checkboxes
			else if (matches.length>1){
				matches.each (function (match, index){
					if (match.checked) value = match.value; 
				});				
			}
			//if (elem) elem.value = item.value;
			if (value == item['default'] || value == ""){
				valid = false;
				// TODO add message together 
				if(item.reqmsg!=""){
					messages.push(item.reqmsg);
				}
			}
		});
		if (!valid){
			message = "";
			messages.each (function (msg, index){message += msg+"\\n";});
			alert(message); 
		}
		return valid;
	}
}
// Disabled for now
/*
window.addEvent("domready",function(){
	var form =document.adminForm;
	if (form){		
		$(form).addEvent('submit',function(event){if (!JevrRequiredFields.verify(form)) {event = new Event(event); event.stop();}});
		//JevrRequiredFields
	};
});
*/


// category conditional fields
var JevrCategoryFields = {
	fields: [],
	cats: $cats,
	setup:function (){
		if (!$('catid')) return;
		var catid = $('catid').value;
		var cats = this.cats[catid];

		// These are the ancestors of this cat
		this.fields.each(function (item,i) {
			var elem = $(document).getElement(".jevplugin_customfield_"+item.name);
			// This is the version that ignores parent category selections
			/*
			// only show it if the selected category is in the list
			if (\$A(item.catids).contains(catid)){
				elem.style.display="table-row";
			}
			else {				
				elem.style.display="none";
			}
			*/
			// hide the item by default
			elem.style.display="none";
			\$A(cats).each (function(cat,i){
				if (\$A(item.catids).contains(cat)){
					//alert("matched "+cat + " cf "+item.catids);
					elem.style.display="table-row";
				}
			});

		});
	}
};
window.addEvent("load",function(){
	if (JevrCategoryFields){
		JevrCategoryFields.setup();
		$('catid').addEvent('change',function(){
			JevrCategoryFields.setup();
		});
		if (!$('ics_id')) return;
		$('ics_id').addEvent('change',function(){
			setTimeout("JevrCategoryFields.setup()",500);
		});
	}
});
SCRIPT;
		$doc->addScriptDeclaration ( $script );

		$params = $this->getParams ( $name, $group );

		if ($description = $this->_xml [$group]->attributes ( 'description' )) {
			// add the params description to the display
			$desc = JText::_ ( $description );
			$customfield = array("label"=>"","input"=>$desc);
			$customfields["customfield_".$node->attributes ( 'name' )] = $customfield;
		}

		$nodes = $this->_xml [$group]->children ();
		for($p = 0; $p < count ( $params ); $p ++) {
			$param = $params [$p];

			$node = $nodes [$p];

			$task = JRequest::getCmd('task', 'cpanel.show');
			// default state of allow override is TRUE
			$allowoverride = $node->attributes("allowoverride");
			if (!is_null($allowoverride) && $allowoverride!=0) $allowoverride=1;
			if ($task == "icalrepeat.edit" && !$allowoverride) continue;

			// Disabled for now
			$required = $node->attributes ( 'required' ) ? JText::_ ( "JEV REQUIRED" ) : "";
			//$required = "";
			$customfield = array();
			$customfield["label"]="";
			if ($param [0]) {
				if (isset ( $param [2] )) {
					$customfield["label"]='<span class="editlinktip">' . $param [0] . $required . '</span>';
				} else {
					$customfield["label"]=JText::_ ( $param [3] ) . $required ;
				}
			}
			$customfield["input"] = $param [1] ;
			$customfields["customfield_".$node->attributes ( 'name' )] = $customfield;

			if ($required) {
				//get the type of the parameter
				$type = $node->attributes ( 'type' );
				if (strpos($type,"jevr")===0){
					$type = "jevcf".substr($type,4);
				}

				$element = & $this->loadElement ( $type );
				if  (method_exists($element,"fetchRequiredScript")){
					$script = $element->fetchRequiredScript($node->attributes ( 'name' ), $node, $name);
					$doc->addScriptDeclaration ( $script );
				}
				else {
					$script = "JevrRequiredFields.fields.push({'name':'".$name."[".$node->attributes ( 'name' )."]', 'default' :'".$node->attributes('default') ."' ,'reqmsg':'".trim(JText::_($node->attributes('requiredmessage'),true))."'}); ";
					$doc->addScriptDeclaration ( $script );
				}
			}

			$catrestrictions = $node->attributes ( 'categoryrestrictions' );
			if ($catrestrictions) {
				static $done;
				if (!isset($done)){
					$done = array();
				}
				if (!in_array($node->attributes ( 'name' ).$name, $done)){
					$done[]=$node->attributes ( 'name' ).$name;;

					$cats = explode(",",$node->attributes("categoryrestrictions"));

					//get the type of the parameter
					$type = $node->attributes ( 'type' );
					if (strpos($type,"jevr")===0){
						$type = "jevcf".substr($type,4);
					}
					$element = & $this->loadElement ( $type );
					if  (method_exists($element,"fetchCategoryRestrictionScript")){
						$script = $element->fetchCategoryRestrictionScript($node->attributes ( 'name' ), $node, $name, $cats);
						$doc->addScriptDeclaration ( $script );
					}
					else {
						$script = "JevrCategoryFields.fields.push({'name':'".$node->attributes ( 'name' )."', 'default' :'".$node->attributes('default') ."' ,'catids':".  json_encode($cats)."}); ";
						$doc->addScriptDeclaration ( $script );
					}

				}
			}


		}

		
		return true;
}


/**
	 * Render a parameter type
	 *
	 * @param	object	A param tag node
	 * @param	string	The control name
	 * @return	array	Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
public function getParam(&$node, $control_name = 'custom_', $group = '_default')
{
	//get the type of the parameter
	$type = $node->attributes('type');

	//remove any occurance of a mos_ prefix
	$type = str_replace('mos_', '', $type);

	if (strpos($type,"jevr")===0){
		$type = "jevcf".substr($type,4);
	}
	$element =& $this->loadElement($type);

	// error happened
	if ($element === false)
	{
		$result = array();
		$result[0] = $node->attributes('name');
		$result[1] = JText::_('Element not defined for type').' = '.$type;
		$result[3] =  $node->attributes('label');
		$result[5] = $result[0];
		return $result;
	}

	// set the rsvpdata for reference
	$element->event = $this->event;
	
	//get value
	$value = $this->get($node->attributes('name'), $node->attributes('default'), $group);

	return $element->render($node, $value, $control_name);
}


public function  renderToBasicArray($name = 'params', $group = '_default')
{

	if (!isset($this->_xml[$group])) {
		return false;
	}
	$results = array();
	foreach ($this->_xml[$group]->children() as $node)  {

		if ($node->attributes('categoryrestrictions')){
			$cats = explode(",",$node->attributes('categoryrestrictions'));
			JArrayHelper::toInteger($cats);

			if (isset($this->event) && !in_array($this->event->catid(),$cats)) continue;
		}
		$result = array();
		$result['type'] = $node->attributes('type');
		if (strpos($result['type'],"jevr")===0){
			$result['type'] = "jevcf".substr($result['type'],4);
		}

		$result['value'] = $this->get($node->attributes('name'), $node->attributes('default'), $group);

		$element =& $this->loadElement($result['type']);
		
		// Add the event row into the $element so that it is available is necessary
		$element->event = $this->event;
		
		if (method_exists($element,"convertValue")) $result['value'] = $element->convertValue($result['value'], $node);

		// reset the type - just in case a special type has changed the node attributes
		$result['type'] = $node->attributes('type');
		if (strpos($result['type'],"jevr")===0){
			$result['type'] = "jevcf".substr($result['type'],4);
		}

		$result['name'] = $node->attributes('name');
		$result['label'] = $node->attributes('label');
		$result['access'] = $node->attributes('access');
		$result['hiddenvalue'] = $node->attributes('hiddenvalue');
		$result['userid'] = $node->attributes('userid');

		$results[$result['name']] = $result;
	}
	return $results;
}

public function constructFilters($group = '_default'){
	$this->filterElements = array();
	if (!isset($this->_xml[$group])) {
		return;
	}
	foreach ($this->_xml[$group]->children() as $node)  {
		if ( !$node->attributes('filter')) continue;
		$type = $node->attributes('type');
		if (strpos($type,"jevr")===0){
			$type = "jevcf".substr($type,4);
		}
		// Must be a new one
		$element =& $this->loadElement( $type, true);
		if (method_exists($element,"constructFilter")) $element->constructFilter($node);
		$this->filterElements[]=$element;
	}
	return ;
}

public function createFilters(){
	$results = array();
	foreach ($this->filterElements as $element) {
		if (method_exists($element,"createFilter"))  {
			$result = $element->createFilter();
			if ($result) $results[] = $result;
		}
	}
	return  implode(" AND ",$results);
}

public function createJoinFilters(){
	$results = array();
	foreach ($this->filterElements as $element) {
		if (method_exists($element,"createJoinFilter")) {
			$result = $element->createJoinFilter();
			if ($result)$results[] = $result;
		}

	}
	return  implode(" LEFT JOIN ",$results);
}

public function setSearchKeywords( & $extrajoin){
	$results = array();
	foreach ($this->filterElements as $element) {
		if (method_exists($element,"setSearchKeywords")) {
			$result = $element->setSearchKeywords($extrajoin);
			if ($result)$results[] = $result;
		}
	}	
	return $results;
}

public function createFiltersHTML(){
	$results = array();
	$results["merge"]=array();
	foreach ($this->filterElements as $element) {
		if (method_exists($element,"createFilterHTML")) $results["merge"][] = $element->createFilterHTML();
	}
	return $results;
}

}