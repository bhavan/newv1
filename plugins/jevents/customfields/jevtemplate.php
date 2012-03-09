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
CREATE TABLE IF NOT EXISTS #__jev_customfields(
	id int(11) NOT NULL auto_increment,
	evdet_id int(11) NOT NULL default 0,
	name varchar(255) NOT NULL default '',
	value text NOT NULL default '',

	PRIMARY KEY  (id),
	INDEX (evdet_id),
	INDEX combo (name,value(10))
) TYPE=MyISAM $charset;	
SQL;
			$db->setQuery($sql);
			if (!$db->query()){
				echo $db->getErrorMsg();
			}

			// If upgrading then add new columns - do all the tables at once
			$sql = "SHOW COLUMNS FROM `#__jev_customfields`";
			$db->setQuery( $sql );
			$cols = $db->loadObjectList();
			$uptodate = false;
			foreach ($cols as $col) {
				if ($col->Field=="evdet_id"){
					$uptodate = true;
					break;
				}
			}
			if (!$uptodate){
				$sql = "ALTER TABLE #__jev_customfields ADD COLUMN evdet_id int(11) NOT NULL default 0";
				$db->setQuery($sql);
				@$db->query();


			}

		}

		$content= "";
		jimport("joomla.utilities.file");
		$templates = JFolder::files(dirname(__FILE__)."/templates/",".xml");
		// only offer extra fields templates if there is more than one available
		if (count($templates)>1){

			// this loads the language strings ! BIZZARE!
			JPluginHelper::importPlugin('jevents');
			// I can't do this since it only returns published plugins and I may want to configure an unpublished plugin!
			//$plugin = JPluginHelper::getPlugin("jevents","jevcustomfields");
			$db=JFactory::getDBO();
			$db->setQuery('SELECT folder AS type, element AS name, params  FROM #__plugins where folder="jevents" and element="jevcustomfields" ');
			$plugin = $db->loadObject();
			ob_start();
				?>
		<fieldset>
			<legend><?php echo  JText::_("JEV EXTRA FIELDS");?></legend>
			<div>
				<label for="custom_rsvp_template" class='label'><?php echo JText::_("JEV EXTRA FIELDS TEMPLATE");?></label>
				<?php
				$options = array();
				$options[] = JHTML::_('select.option', "", JText::_("JEV SELECT TEMPLATE"), 'var', 'text');
				foreach ($templates as $template) {
					if ($template=="fieldssample.xml") continue;
					$options[] = JHTML::_('select.option', $template, ucfirst(str_replace(".xml","",$template)), 'var', 'text');
				}

				$value = "";
				if(!is_null($plugin)){
					$params = new JParameter($plugin->params);
					$value = $params->get("template","");
				}

				echo JHTML::_('select.genericlist',  $options, $control_name.'['.$name.']', '', 'var', 'text', $value);
				?>
			</div>
		</fieldset>
		<?php
		$content = ob_get_clean();
		}
		return $content;

		$rows = $node->attributes('rows');
		$cols = $node->attributes('cols');
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace('<br />', "\n", JText::_($value));

		return '<textarea name="'.$control_name.'['.$name.']" cols="'.$cols.'" rows="'.$rows.'" '.$class.' id="'.$control_name.$name.'" >'.$value.'</textarea>';
	}

}