<?php
/**
 * JEvents Component for Joomla 1.5.x
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementJevtemplate extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'Jevtemplate';

	function fetchElement($name, $value, &$node, $control_name)
	{
		JPlugin::loadLanguage( 'plg_jevents_jevcustomfields',JPATH_ADMINISTRATOR );

		// While I'm here do the database setup and upgrades
		static $dbchecked=false;
		if (!$dbchecked){
			$dbchecked=true;
			$db = & JFactory::getDBO();
			$charset = ($db->hasUTF()) ? 'DEFAULT CHARACTER SET `utf8`' : '';
			$sql = <<<SQL
CREATE TABLE IF NOT EXISTS #__jev_customfields2(
	id int(11) NOT NULL auto_increment,
	target_id int(11) NOT NULL default 0,
	targettype varchar(255) NOT NULL default '',
	name varchar(255) NOT NULL default '',
	value text NOT NULL default '',

	PRIMARY KEY  (id),
	INDEX (target_id, targettype),
	INDEX combo (name,value(10))
) TYPE=MyISAM $charset;	
SQL;
			$db->setQuery($sql);
			if (!$db->query()){
				echo $db->getErrorMsg();
			}

			// If upgrading then add new columns - do all the tables at once
			$sql = "SHOW COLUMNS FROM `#__jev_customfields2`";
			$db->setQuery( $sql );
			$cols = $db->loadObjectList();
			$uptodate = false;
			foreach ($cols as $col) {
				if ($col->Field=="target_id"){
					$uptodate = true;
					break;
				}
			}
			if (!$uptodate){
				$sql = "ALTER TABLE #__jev_customfields2 ADD COLUMN target_id int(11) NOT NULL default 0";
				$db->setQuery($sql);
				@$db->query();
			}
		}

		$content= "";
		jimport("joomla.utilities.file");
		if (JFolder::exists(JPATH_SITE."/plugins/jevents/customfields/templates/")){
			$templates = JFolder::files(JPATH_SITE."/plugins/jevents/customfields/templates/",".xml");
			// only offer extra fields templates if there is more than one available
			if (count($templates)>1){

				JPluginHelper::importPlugin('jevents');
				$plugin = JPluginHelper::getPlugin("jevents","jevcustomfields");
				ob_start();
				$options = array();
				$options[] = JHTML::_('select.option', "", JText::_("JEV SELECT TEMPLATE"), 'var', 'text');
				foreach ($templates as $template) {
					if ($template=="fieldssample.xml") continue;
					$options[] = JHTML::_('select.option', $template, ucfirst(str_replace(".xml","",$template)), 'var', 'text');
				}

				echo JHTML::_('select.genericlist',  $options, $control_name.'['.$name.']', '', 'var', 'text', $value);
				$content = ob_get_clean();
			}
			return $content;
		}
		else return "";

		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", JText::_($value));

		return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
	}

}