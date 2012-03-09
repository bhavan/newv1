<?php
/**
 * RokTabs Module
 *
 * @package		Joomla
 * @subpackage	RokTabs Module
 * @copyright Copyright (C) 2009 RocketTheme. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see RT-LICENSE.php
 * @author RocketTheme, LLC
 *
 */
// no direct access
defined( '_JEXEC' ) or die('Restricted access');

require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

class modRokTabsHelper
{
	
	function getList(&$params)
	{
		global $mainframe;

		$db			=& JFactory::getDBO();
		$user		=& JFactory::getUser();
		$userId		= (int) $user->get('id');
		
		$count		    = 15; 
		$catid		    = trim( $params->get('catid') );
		$secid		    = trim( $params->get('secid') );
		$show_front	    = $params->get('show_front', 1);
		$aid		    = $user->get('aid', 0);
		$content_type   = $params->get('content_type','joomla');
		$ordering       = $params->get('itemsOrdering');
		$cid            = $params->get('category_id', NULL);
        $text_length    = intval($params->get( 'preview_count', 200) );

		$contentConfig = &JComponentHelper::getParams( 'com_content' );
		$access		= !$contentConfig->get('shownoauth');

		$nullDate	= $db->getNullDate();

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

        $where = '';
		
		// ensure should be published
		$where .= " AND ( a.publish_up = ".$db->Quote($nullDate)." OR a.publish_up <= ".$db->Quote($now)." )";
		$where .= " AND ( a.publish_down = ".$db->Quote($nullDate)." OR a.publish_down >= ".$db->Quote($now)." )";
		
	    // ordering
		switch ($ordering) {
			case 'date' :
				$orderby = 'a.created ASC';
				break;
			case 'rdate' :
				$orderby = 'a.created DESC';
				break;
			case 'alpha' :
				$orderby = 'a.title';
				break;
			case 'ralpha' :
				$orderby = 'a.title DESC';
				break;
			case 'order' :
				$orderby = 'a.ordering';
				break;
			default :
				$orderby = 'a.id DESC';
				break;
		}

		// content specific stuff
        if ($content_type=='joomla') {
            // start Joomla specific
            
            $catCondition = '';
            $secCondition = '';

            if ($show_front != 2) {
        		if ($catid)
        		{
        			$ids = explode( ',', $catid );
        			JArrayHelper::toInteger( $ids );
        			$catCondition = ' AND (cc.id=' . implode( ' OR cc.id=', $ids ) . ')';
        		}
        		if ($secid)
        		{
        			$ids = explode( ',', $secid );
        			JArrayHelper::toInteger( $ids );
        			$secCondition = ' AND (s.id=' . implode( ' OR s.id=', $ids ) . ')';
        		}
        	}
		
    		// Content Items only
    		$query = 'SELECT a.*, ' .
    			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
    			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
    			' FROM #__content AS a' .
    			($show_front == '0' ? ' LEFT JOIN #__content_frontpage AS f ON f.content_id = a.id' : '') .
    			($show_front == '2' ? ' INNER JOIN #__content_frontpage AS f ON f.content_id = a.id' : '') .
    			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
    			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
    			' WHERE a.state = 1'. $where .' AND s.id > 0' .
    			($access ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
    			($catid && $show_front != 2 ? $catCondition : '').
    			($secid && $show_front != 2 ? $secCondition : '').
    			($show_front == '0' ? ' AND f.content_id IS NULL ' : '').
    			' AND s.published = 1' .
    			' AND cc.published = 1' .
    			' ORDER BY '. $orderby;
    		// end Joomla specific
	    } else {
		    // start K2 specific
		    require_once (JPATH_SITE.DS.'components'.DS.'com_k2'.DS.'helpers'.DS.'route.php');
		    
    		$query = "SELECT a.*, c.name as categoryname,c.id as categoryid, c.alias as categoryalias, c.params as categoryparams".
    		" FROM #__k2_items as a".
    		" LEFT JOIN #__k2_categories c ON c.id = a.catid";
	
    		$query .= " WHERE a.published = 1"
    		." AND a.access <= {$aid}"
    		." AND a.trash = 0"
    		." AND c.published = 1"
    		." AND c.access <= {$aid}"
    		." AND c.trash = 0"
    		;
	
   		    if ($params->get('catfilter')){
    			if (!is_null($cid)) {
    				if (is_array($cid)) {
    					$query .= " AND a.catid IN(".implode(',', $cid).")";
    				}
    				else {
    					$query .= " AND a.catid={$cid}";
    				}
    			}
    		}

    		if ($params->get('FeaturedItems')=='0')
    			$query.= " AND a.featured != 1";
	
    		if ($params->get('FeaturedItems')=='2')
    			$query.= " AND a.featured = 1";
    			
    		$query .= $where . ' ORDER BY ' . $orderby;
    		// end K2 specific
		}
		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();

		// Process the prepare content plugins
		JPluginHelper::importPlugin('content');

		$i		= 0;
		$lists	= array();
		foreach ( $rows as $row )
		{

			$dispatcher   =& JDispatcher::getInstance();
			$results = $dispatcher->trigger('onPrepareContent', array (& $row, & $params, 0));
			
			$text = JHTML::_('content.prepare',$row->introtext,$contentConfig);
			
			$lists[$i]->id = $row->id;
			$lists[$i]->created = $row->created;
			$lists[$i]->modified = $row->modified;
			if ($content_type=='joomla') {
			    $lists[$i]->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
			} else {
			    $lists[$i]->link = JRoute::_(K2HelperRoute::getItemRoute($row->id.':'.$row->alias, $row->catid.':'.$row->categoryalias));
			}
			$lists[$i]->title = htmlspecialchars( $row->title );
			$lists[$i]->introtext = modRokTabsHelper::prepareContent( $text, $text_length);
			$i++;
		}
		
		return $lists;
	}
	
	function write_tabs($tabs, $tabs_position, &$list, $tabs_title, $tabs_incremental, $tabs_hideh6) {
		if ($tabs_position == 'hidden') $tabstyle = 'style="display: none;"'; 
		else $tabstyle = '';

		$return = '';
		$return .= "<div class='roktabs-links' $tabstyle>\n";
		$return .= "<ul class='roktabs-$tabs_position'>\n";
				$tabs = intval($tabs);

				if ($tabs == 0 || $tabs > count($list)) $tabs = count($list);
								
				for($i = 0; $i < $tabs; $i++) {
					if ($list[$i]->introtext != '') {
						$class = '';
						if (!$i) $class = 'class="first active"';
						if ($i == $tabs - 1) $class = 'class="last"';

						switch($tabs_title) {
							case 'incremental':
								if (strlen($tabs_incremental) > 0) $title = $tabs_incremental . '' . ($i + 1);
								else $title = $tabs_incremental . '' . ($i + 1);						
								break;
							case 'h6':
								$regex = '/<h6(?:.+)?>(.+?)<\/h6>/is';
								preg_match($regex, $list[$i]->introtext, $matches);
								if (count($matches) && strlen($matches[1]) > 0) {
									$title = $matches[1];
									if ($tabs_hideh6 == "1") {
										$list[$i]->introtext = str_replace($matches[0], '', $list[$i]->introtext);
									}
									break; 
								}
							default: 
								$title = $list[$i]->title;
						}

						$return .= "<li $class><span>$title</span></li>\n";
					}
				}

		$return .= "</ul>\n";
		$return .= "</div>\n";


		return $return;
	}
	
	function prepareContent( $text, $length=200 ) {
		// strips tags won't remove the actual jscript
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );

		// cut off text at word boundary if required
		if (strlen($text) > $length) {
			//$text = substr($text, 0, strpos($text, ' ', $length)) . "..." ;
		} 
		return $text;
	}
}
