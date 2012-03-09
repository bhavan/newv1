<?php
/**
 * JEventsExtras Component for Joomla 1.5.x
 * Patrick Winkler, Support-Masters
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

// TODO replace with JDate 

class JEventsExtrasHTML{

	/**
	 * Build HTML selection list of categories
	 *
	 * @param int $aid				Selected artistID
	 * @param string $args				Additional HTML attributes for the <select> tag
	 * @param boolean $with_unpublished	Set true to build list with unpublished artists
	 * @param boolean $require_sel		First entry: true = Choose one category, false = All categories
	 * @param boolean $with_global		Set true to build list with global artists
	 * @param string $fieldname		value of the name tag of <select>
	 */
	function buildArtistSelect( $aid, $args, $with_unpublished=true, $require_sel=false, $with_global=true,$fieldname="aid"){
		
		$user =& JFactory::getUser();
		$db	=& JFactory::getDBO();
		
		$catsql = 'SELECT c.art_id, c.title as atitle, c.published ' .
				' FROM #__jev_artists AS c' .
				' WHERE c.access<='.$db->Quote($user->aid);

		if ($with_unpublished) {
			$catsql .= ' AND c.published >= 0';
		} else {
			$catsql .= ' AND c.published = 1';
		}
		
		if ($with_global) {
			$catsql .= ' AND c.global = 1';
		} else {
			$catsql .= ' AND c.global = 0';
		}


		//s$catsql .=" ORDER BY ordering";
		
		$db->setQuery($catsql);
		//echo $db->_sql;
		$rows = $db->loadObjectList();
		
		foreach ($rows as $key=>$option) {
			$title = $option->atitle;
			if (!is_null($option->atitle)){
				$title = $option->atitle;
			}
			$rows[$key]->name = $title;
		}
		JArrayHelper::sortObjects($rows,"name");
		
		$t_first_entry = ($require_sel) ? JText::_('JEV_EVENT_CHOOSE_ARTEG') : JText::_('JEV_EVENT_ALLART');
		//$categories[] = JHTML::_('select.option', '0', JText::_('JEV_EVENT_CHOOSE_CATEG'), 'id', 'name' );
		$artists[] = JHTML::_('select.option', '0', $t_first_entry, 'art_id', 'name' );
		
		if ($with_unpublished) {
			for ($i=0;$i<count($rows);$i++) {
				if ($rows[$i]->published == 0) $rows[$i]->name = $rows[$i]->name . '('. JText::_('JEV_NOT_PUBLISHED') . ')';
			}
		}

		$artists = array_merge( $artists, $rows );
		$alist = JHTML::_('select.genericlist', $artists, $fieldname, $args, 'art_id', 'name', $aid );
		
		return $alist;
	}
}