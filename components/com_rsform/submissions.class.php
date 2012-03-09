<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

// Little memory hack we're not proud of
if (intval(ini_get('memory_limit')) < 128)
	ini_set('memory_limit','128M');

$db = JFactory::getDBO();
$db->setQuery("SET SQL_BIG_SELECTS=1");
$db->query();
	
class SManager
{
	/**
	 * Sets the submission table headers
	 * @values 	string
	 * @default array
	 */
	var $headers = array();

	/**
	 * Sets the current form id
	 * @values 	int
	 * @default null
	 */
	var $formId = null;
	
	/**
	 * Sets the current order header
	 * @values 	int
	 * @default 0
	 */
	var $order = 0;
	
	/**
	 * Sets the current order/sorting
	 * @values	'asc','desc'
	 * @default desc
	 */
	var $direction = 'desc';
	
	/**
	 * Sets the filter
	 * @values	string
	 * @default null
	 */
	var $filter = '';
	
	/**
	 * Sets the current page
	 * @values	int
	 * @default 1
	 */
	var $current = 1;
	
	//$order = array( id of the element, order of the element)
	// order:
	// 0 - Desc
	// 1 - Asc
	// Default value set to 1 - "date_added"
	
	/**
	 * Sets the current limit per page
	 * @values	int
	 * @default 5
	 */
	var $limit = 5;
	
	/**
	 * Sets whether this is an export or not
	 * @values	int
	 * @default 0
	 */
	var $export = 0;
	
	/**
	 * An array that contains all the rows to be exported
	 * Set to 0 to export everything
	 * @values	array
	 * @default 0
	 */
	var $rows = 0;
	
	var $_db;
	
	/**
     * Initialize the class
     *
     * @access   public
	 * @param	 string		The form Id 
     */
	function SManager($formId, $export=0)
	{
		$this->_db = JFactory::getDBO();
		$this->formId = $formId;
		$this->export = $export;
		$this->setHeaders();
	}
	
	/**
     * Initialize the submission table headers into the headers array
     *
     * @access   public
     */
	function setHeaders()
	{
		global $RSadapter, $mainframe;
		
		//Trigger Event - onBeforeLoadHeadersSubmissions
		$mainframe->triggerEvent('rsfp_bk_onBeforeLoadHeadersSubmissions', array(array('SManager'=>&$this)));
		// Hardcoded headers
		$this->headers[] = 'DateSubmitted';
		$this->headers[] = 'Username';
		if ($this->export)
			$this->headers[] = 'UserIp';
		
		// Get the form headers
		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c, #__rsform_properties p WHERE c.ComponentId = p.ComponentId AND p.PropertyName='NAME' AND c.FormId = '".$this->formId."' AND c.Published = 1 ORDER BY c.`Order`");
		$result = $this->_db->loadAssocList();
		foreach ($result as $row)
			$this->headers[] = $row['PropertyValue'];
		
		//Trigger Event - onAfterLoadHeadersSubmissions
		$mainframe->triggerEvent('rsfp_bk_onAfterLoadHeadersSubmissions', array(array('SManager'=>&$this)));
	}

	/**
     * Get the submissions
     *
     * @access   public
	 * @return	 array
     */
	function getSubmissions()
	{
		global $RSadapter,$mainframe;
		$return = array();
		
		//Trigger Event - onBeforeLoadRowsSubmissions
		$mainframe->triggerEvent('rsfp_bk_onBeforeLoadRowsSubmissions', array(array('SManager'=>&$this,'return'=>&$return)));
		
		// Is this an export ?
		if ($this->export)
		{
			if($this->limit) $limitSQL = " LIMIT ".$this->limitstart.",".$this->limit;
			else $limitSQL = "";
						
			// Get data for the submission ids //DISTINCT s.SubmissionId
			$this->_db->setQuery("SELECT DISTINCT s.SubmissionId FROM `#__rsform_submissions` s LEFT JOIN `#__rsform_submission_values` sv ON s.`SubmissionId` = sv.`SubmissionId` WHERE sv.`FormId` = '".$this->formId."' ".($this->rows != 0 ? "AND s.`SubmissionId` IN (".implode(',',$this->rows).")" : "")." ORDER BY `s`.`SubmissionId` DESC".$limitSQL);
			$submissionIds = $this->_db->loadObjectList();
			
			$result = array();
			foreach($submissionIds as $submissionId)
			{
				$this->_db->setQuery("SELECT * FROM `#__rsform_submissions` s LEFT JOIN `#__rsform_submission_values` sv ON s.`SubmissionId` = sv.`SubmissionId` WHERE s.`SubmissionId` =".$submissionId->SubmissionId);
				
				$result = array_merge($result,$this->_db->loadAssocList());
			}
			
			//$result = $this->_db->loadAssocList();
			//print_r($result);die();
		}
		// Not an export, just show the submissions for the current page
		else
		{
			$filter = '';
			// Optimization hack - we don't need the filter running everytime we call the page
			if ($this->filter != '')
				$filter .= " AND (
				sv.`FieldValue` LIKE '%{$this->filter}%' OR
				s.`DateSubmitted` LIKE '%{$this->filter}%' OR
				s.`Username` LIKE '%{$this->filter}%' OR
				s.`UserIp` LIKE '%{$this->filter}%')" ;
		
			if ($this->order > 1)
				$order = 'sv.`FieldValue`';
			else
				$order = 's.`'.RScleanVar($this->headers[$this->order]).'`';
			
			$this->current--;
			
			// Get submission ids first
			$submission_ids = array();

			$this->_db->setQuery("SELECT DISTINCT sv.`SubmissionId`, s.* FROM `#__rsform_submissions` s LEFT JOIN `#__rsform_submission_values` sv ON s.`SubmissionId` = sv.`SubmissionId` WHERE sv.`FormId` = '".$this->formId."' ".($this->order > 1 ? " AND sv.`FieldName`='".RScleanVar($this->headers[$this->order])."'" : "")." ".$filter." ORDER BY ".$order." ".$this->direction." LIMIT ".$this->current*$this->limit.",".$this->limit);
			$result = $this->_db->loadAssocList();
			
			foreach ($result as $row)
			{
				$submission_ids[] = $row['SubmissionId'];
				$return[$row['SubmissionId']]['FormId'] = $row['FormId'];
				$return[$row['SubmissionId']]['DateSubmitted'] = $row['DateSubmitted'];
				$return[$row['SubmissionId']]['UserIp'] = $row['UserIp'];
				$return[$row['SubmissionId']]['Username'] = $row['Username'];
				$return[$row['SubmissionId']]['UserId'] = $row['UserId'];
				$return[$row['SubmissionId']]['SubmissionValues'] = array();
			}
			
			if (count($submission_ids) == 0)
				return $return;
			
			$this->_db->setQuery("SELECT * FROM `#__rsform_submission_values` WHERE `SubmissionId` IN (".implode(',',$submission_ids).")");
			$result = $this->_db->loadAssocList();
		}
		
		if ($this->export)
		{
			foreach ($result as $row)
			{
				$return[$row['SubmissionId']]['FormId'] = $row['FormId'];
				$return[$row['SubmissionId']]['DateSubmitted'] = $row['DateSubmitted'];
				$return[$row['SubmissionId']]['UserIp'] = $row['UserIp'];
				$return[$row['SubmissionId']]['Username'] = $row['Username'];
				$return[$row['SubmissionId']]['UserId'] = $row['UserId'];
				$return[$row['SubmissionId']]['SubmissionValues'][$row['FieldName']] = array('Value' => $row['FieldValue'], 'Id' => $row['SubmissionValueId']);
			}
		}
		else
			foreach ($result as $row)
				$return[$row['SubmissionId']]['SubmissionValues'][$row['FieldName']] = array('Value' => $row['FieldValue'], 'Id' => $row['SubmissionValueId']);
		
		//Trigger Event - onAfterLoadRowsSubmissions
		$mainframe->triggerEvent('rsfp_bk_onAfterLoadRowsSubmissions', array(array('SManager'=>&$this,'return'=>&$return)));	
		
		return $return;
	}

	/**
     * Create the HTML code for the submission table headers
     *
     * @access   public
     */
	function createHeaders()
	{
		$return = '';
		foreach ($this->headers as $header_id => $header_name)
		{
			$return .= '<th id="field'.$header_id.'" class="title" style="white-space:nowrap;">'.$header_name;
			$return .= '<a href="javascript:void(0)" onclick="sortRows('.$header_id.', \'desc\')"><img src="images/'.($this->order == $header_id && $this->direction == 'desc' ? 'downarrow-1.png' : 'downarrow.png').'" border="0" /></a>';
			$return .= '<a href="javascript:void(0)" onclick="sortRows('.$header_id.', \'asc\')"><img src="images/'.($this->order == $header_id && $this->direction == 'asc' ? 'uparrow-1.png' : 'uparrow.png').'" border="0" /></a>';
		}
		echo $return;
	}
	
	/**
     * Create the HTML code for the submission table rows
     *
     * @access   public
     */
	function createRows()
	{
		global $RSadapter, $mainframe;
		
		$selected = '';
		$selected = "z, ".$selected;
		$ids = explode(",", $selected);
		$i = 0;
		$rowColor = 0;
		
		$totalHeaders = count($this->headers) - 2;
		
		// Get all components from the form
		$components = array();
		
		$this->_db->setQuery("SELECT #__rsform_properties.PropertyValue, #__rsform_components.ComponentTypeId FROM #__rsform_properties LEFT JOIN #__rsform_components ON #__rsform_properties.ComponentId = #__rsform_components.ComponentId WHERE #__rsform_properties.PropertyName = 'NAME' AND #__rsform_components.FormId='".$this->formId."' AND #__rsform_components.Published = 1 ORDER BY #__rsform_components.Order");
		$result = $this->_db->loadAssocList();
		foreach ($result as $row)
			$components[$row['PropertyValue']]= $row['ComponentTypeId'];
		
		
		// Get submissions
		$submissions = $this->getSubmissions();
		
		//Trigger Event - onAfterLoadRowsSubmissions
		$mainframe->triggerEvent('rsfp_bk_onAfterLoadComponents', array(array('SManager'=>&$this,'components'=>&$components,'submissions'=>&$submissions)));	
		
		
		foreach ($submissions as $submission_id => $submission)
		{
			$rowColor = ($rowColor == 0 ? 1 : 0);
			$j = 0;
			if (!isset($submission['Username'])) $submission['Username'] = '-';
			
			echo '<tr class="row'.$rowColor.'">
			<td><input name="checks[]" value="'.$submission_id.'" type="checkbox" id="cb'.$i.'" onclick="checkOne(this); isChecked(this.checked)" /></td>
			<td>'.$submission['DateSubmitted'].'</td>
			<td>'.$submission['Username'].'</td>';
			
			foreach ($components as $component_name => $component_type_id)
			{
				if (!isset($submission['SubmissionValues'][$component_name]))
				{
					$submission['SubmissionValues'][$component_name]['Value'] = '';
					$submission['SubmissionValues'][$component_name]['Id'] = '';
				}
					
				// Check whether this component is a file upload
				if ($component_type_id == 9)
				{
					//get file					
					$filename = basename($submission['SubmissionValues'][$component_name]['Value']);
					
					$fullpath = realpath($submission['SubmissionValues'][$component_name]['Value']);
					$homepath = $RSadapter->config['absolute_path'];
					$livesite = $RSadapter->config['live_site'];
					
					$urlpath = str_replace($homepath, $livesite, $fullpath);
					$urlpath = str_replace('\\', '/', $urlpath);
					/*$urlpath = str_replace('//', '/', $urlpath);*/
					
					/*
					$submission['SubmissionValues'][$component_name]['Value'] = str_replace($RSadapter->config['absolute_path'],$RSadapter->config['live_site'],$submission['SubmissionValues'][$component_name]['Value']);
					$submission['SubmissionValues'][$component_name]['Value'] = str_replace(array('//','\\\\','http:/','https:/'),array('/','\\','http://','https://'),$submission['SubmissionValues'][$component_name]['Value']);
					*/
					$label = '<a href="'.$urlpath.'">'.$filename.'</a>'; 
				}
				else 
					$label = $submission['SubmissionValues'][$component_name]['Value'];
				
				echo '<td>
				<div id="row-'.$submission_id.'-'.$j.'">'.$label.'</div>
				<textarea id="textarea-'.$submission_id.'-'.$j.'" name="textarea-'.$submission_id.'" class="hidden">'.htmlspecialchars($submission['SubmissionValues'][$component_name]['Value']).'</textarea>
				<input type="hidden" name="SubmissionValueId-'.$submission_id.'" value="'.$submission['SubmissionValues'][$component_name]['Id'].'" />
				<input type="hidden" name="fieldName-'.$submission_id.'" value="'.$component_name.'" />
				</td>';
				$j++;	
			}
			echo '<td width="195">
				<div id="act-'.$submission_id.'">
				<input type="button" name="edit" onclick="editRow('.$submission_id.', '.$totalHeaders.')" value="'._RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_EDIT.'" />
				<input type="button" name="remove" onclick="removeRow('.$submission_id.')" value="'._RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_REMOVE.'" />
				<input type="button" name="resend" onclick="resendRow('.$submission_id.', this)" value="'._RSFORM_BACKEND_SUBMISSIONS_MANAGE_TABLE_ACTION_RESEND.'" />
				</div>
				</td>
				</tr>'."\n";
		}
	}

	function createExportFile()
	{
		global $RSadapter;
		
	//	echo $this->exportFile; die();
		
		if($this->limitstart == 0)
			$handle = fopen($this->exportFile, 'w');
		else
			$handle = fopen($this->exportFile, 'a');
		
		// Remove from exportOrder headers that we're not going to export
		foreach ($this->headers as $header)
			if (!isset($this->exportSubmission[$header]) && !isset($this->exportComponent[$header]))
				unset($this->exportOrder[$header]);
		
		// Sort ascending the exportOrder array
		$this->exportOrder = array_flip($this->exportOrder);
		ksort($this->exportOrder);

		// How many submissions are we going to export ? 0 for all, else use an array that specifies the submission ids
		$this->rows = 0;
		if (!empty($_POST['ExportRows']))
		{
			$rows = explode(',',$_POST['ExportRows']);
			foreach ($rows as $i=>$row)
				$rows[$i] = intval($row);
			
			$this->rows = $rows;
		}
		
		$submissions = $this->getSubmissions();	
		if(empty($submissions)) {
			echo 'END';exit();
		}
		/*
		header('Expires: Mon, 01 Jan 1999 01:00:00 GMT');
		header('Last-Modified: '.gmdate('D,d M YH:i:s').' GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: application/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.date('Y-m-d').'_rsform.csv');
		*/
		// Use headers? If so, first line of output contains the component names
		if ($this->exportHeaders && $this->limitstart == 0)
		{
			fwrite($handle, $this->exportFieldEnclosure.implode($this->exportFieldEnclosure.$this->exportDelimiter.$this->exportFieldEnclosure,$this->exportOrder).$this->exportFieldEnclosure);
			fwrite($handle, "\n");
		}
		
		foreach ($submissions as $submissionId => $submission)
		{
			foreach ($this->exportOrder as $orderId => $header)
			{
				if (isset($submission['SubmissionValues'][$header]))
				{
					$submission['SubmissionValues'][$header]['Value'] = ereg_replace("\015(\012)?", "\012", $submission['SubmissionValues'][$header]['Value']);
					if(strpos($submission['SubmissionValues'][$header]['Value'],"\n") !== FALSE)
						$submission['SubmissionValues'][$header]['Value'] = str_replace("\n",' ',$submission['SubmissionValues'][$header]['Value']);
				}
				fwrite($handle, $this->exportFieldEnclosure.(isset($submission['SubmissionValues'][$header]) ? str_replace(array('\\r','\\n','\\t',$this->exportFieldEnclosure), array("\015","\012","\011",$this->exportFieldEnclosure.$this->exportFieldEnclosure), $submission['SubmissionValues'][$header]['Value']) : (isset($submission[$header]) ? $submission[$header] : '')).$this->exportFieldEnclosure.($header != end($this->exportOrder) ? $this->exportDelimiter : ""));
			}
			fwrite($handle, "\n");
			
		}
		fclose($handle);
		//echo $_SESSION['rsfp_post'];//$this->limitstart+$this->limit;
		exit;
	}

	function deleteRow($id)
	{
		global $RSadapter;
		$id = intval($id);
		$this->_db->setQuery("DELETE FROM #__rsform_submission_values WHERE `SubmissionId` = '".$id."'");
		$this->_db->query();
		
		$this->_db->setQuery("DELETE FROM #__rsform_submissions WHERE `SubmissionId` = '".$id."'");
		$this->_db->query();
	}

	function setOrder($field, $type)
	{
		$this->direction = $type == 1 ? 'asc' : 'desc';
		$this->order = $field;
	}
	
	function pager($current=1)
	{
		global $RSadapter;
		
		$filter = '';
		// Optimization hack - we don't need the filter running everytime we call the page
		if ($this->filter != '')
			$filter .= " AND (
			sv.`FieldValue` LIKE '%{$this->filter}%' OR
			s.`DateSubmitted` LIKE '%{$this->filter}%' OR
			s.`Username` LIKE '%{$this->filter}%' OR
			s.`UserIp` LIKE '%{$this->filter}%')";
			
		$page = $current;
		
		// Get submission ids first
		$submission_ids = array();
		$this->_db->setQuery("SELECT COUNT(DISTINCT(sv.`SubmissionId`)) FROM #__rsform_submissions s LEFT JOIN #__rsform_submission_values sv ON s.`SubmissionId` = sv.`SubmissionId` WHERE sv.`FormId` = '".$this->formId."' ".($this->order > 1 ? " AND sv.`FieldName`='".RScleanVar($this->headers[$this->order])."'" : "")." ".$filter);
		$total = $this->_db->loadResult();

		$last = ceil($total/$this->limit);
		
		echo '<div class="pagenav">';
		
		if ($page > 1)
			echo '<span><a href="javascript:void(0)" onclick="changePage(1)">&lt;&lt;Start</a></span>
			<span><a href="javascript:void(0)" onclick="changePage('.($page-1).')">&lt;Previous</a></span>';
		else
			echo '<span>&lt;&lt;Start</span>
			<span>&lt;Previous</span>';
		
		for ($i=1;$i<=$last;$i++)
		{
			if ($i == $page)
				echo '<span class="selected">'.$i.'</span>';
			else
				echo '<span><a href="javascript:void(0)" onclick="changePage('.$i.')">'.$i.'</a></span>';
		}
		
		if ($page < $last)
			echo '<span><a href="javascript:void(0)" onclick="changePage('.($page+1).')">Next&gt;</a></span>';
		else
			echo '<span>Next&gt;</span>';
			
		
		if ($page < $last)
			echo '<span><a href="javascript:void(0)" onclick="changePage('.$last.')">End&gt;&gt;</a></span>';
		else
			echo '<span>End&gt;&gt;</span>';

		$rows = ($current-1) * $this->limit + 1;
		$shown = $rows + $this->limit - 1;
		if ($shown > $total)
			$shown = $total;
		echo '<div class="statistics">Results '.$rows.' - '.$shown.' of '.$total.'</div></div>';
	}

	function setValue($SubmissionId, $SubmissionValueId, $value, $fieldName=null)
	{
		$RSadapter = $GLOBALS['RSadapter'];
		$SubmissionId = intval($SubmissionId);
		$SubmissionValueId = intval($SubmissionValueId);
		$value = RScleanVar($value);
		$fieldName = RScleanVar($fieldName);
		
		if(empty($SubmissionValueId))
			$this->_db->setQuery("INSERT INTO #__rsform_submission_values SET `SubmissionId`='".$SubmissionId."', `FormId`='".$this->formId."', `FieldName`='".$fieldName."', `FieldValue`='".$value."'");
		else
			$this->_db->setQuery("UPDATE #__rsform_submission_values SET `FieldValue` = '".$value."' WHERE `SubmissionValueId` = '".$SubmissionValueId."' LIMIT 1");
		$this->_db->query();
	}
}
?>