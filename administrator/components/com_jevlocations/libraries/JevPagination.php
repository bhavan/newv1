<?php
/**
 * Copyright (C) 2009 GWE Systems Ltd
 *
 * All rights reserved.
 *
*/
defined('_VALID_MOS') or defined('_JEXEC') or die( 'No Direct Access' );

jimport("joomla.html.pagination");

class JevPagination extends JPagination {


	var $asForm = false;

	/**
	 * Constructor
	 *
	 * @param	int		The total number of items
	 * @param	int		The offset of the item to start at
	 * @param	int		The number of items to display per page
	 * @param	boolean	Display pagination as form
	 */	
	function __construct($total, $limitstart, $limit, $asform=false)
	{
		$this->asForm = $asform;
		return parent::__construct($total, $limitstart, $limit);
	}

	function _list_footer($list)
	{
		// Initialize variables
		$lang =& JFactory::getLanguage();
		$html = "<div class=\"container\"><div class=\"pagination\">\n";

		$html .= "\n<div class=\"limit\">".JText::_('Display Num').$list['limitfield']."</div>";
		$html .= $list['pageslinks'];
		$html .= "\n<div class=\"limit\">".$list['pagescounter']."</div>";

		$html .= "\n<input type=\"hidden\" name=\"limitstart\" value=\"".$list['limitstart']."\" />";
		$html .= "\n</div></div>";

		return $html;
	}

	function _list_render($list)
	{
		if ($this->asForm){
			// Initialize variables
			$html = "";

			// Reverse output rendering for right-to-left display
			$list['start']['data'] = str_replace('value="','value="&lt;&lt; ',$list['start']['data']);
			$list['previous']['data'] = str_replace('value="','value="&lt; ',$list['previous']['data']);
			$list['next']['data'] = preg_replace('/value="(.*)"/','value="$1 &gt;"',$list['next']['data']);
			$list['end']['data'] = preg_replace('/value="(.*)"/','value="$1 &gt;&gt;"',$list['end']['data']);

			/*
			$html .= $list['start']['data'];
			$html .= ' '.$list['previous']['data'];
			foreach( $list['pages'] as $page ) {
			$html .= ' '.$page['data'];
			}
			$html .= ' '. $list['next']['data'];
			$html .= ' '. $list['end']['data'];
			*/

			if ($list['start']['active']) {
				$html .= "<div class=\"jevbutton-right\"><div class=\"start\">".$list['start']['data']."</div></div>";
			} else {
				$html .= "<div class=\"jevbutton-right off\"><div class=\"start\">".$list['start']['data']."</div></div>";
			}
			if ($list['previous']['active']) {
				$html .= "<div class=\"jevbutton-right\"><div class=\"prev\">".$list['previous']['data']."</div></div>";
			} else {
				$html .= "<div class=\"jevbutton-right off\"><div class=\"prev\">".$list['previous']['data']."</div></div>";
			}

			$html .= "\n<div class=\"jevbutton-left\"><div class=\"page\">";
			foreach( $list['pages'] as $page ) {
				$html .= $page['data'];
			}
			$html .= "\n</div></div>";

			if ($list['next']['active']) {
				$html .= "<div class=\"jevbutton-left\"><div class=\"next\">".$list['next']['data']."</div></div>";
			} else {
				$html .= "<div class=\"jevbutton-left off\"><div class=\"next\">".$list['next']['data']."</div></div>";
			}
			if ($list['end']['active']) {
				$html .= "<div class=\"jevbutton-left\"><div class=\"end\">".$list['end']['data']."</div></div>";
			} else {
				$html .= "<div class=\"jevbutton-left off\"><div class=\"end\">".$list['end']['data']."</div></div>";
			}

			return $html;
		}
		else {
			return parent::_list_render($list);
		}
	}

	function _item_active(&$item)
	{
		if ($this->asForm){
			$base = $item->base>0 ? $item->base:0;
			//return "<input type='button' title=\"".$item->text."\" onclick=\"this.form.limitstart.value=$base;submitform();return false;\" class=\"pagenav\" value=\"$item->text\" />";
			return "<a  title=\"".$item->text."\" href=\"javascript:document.adminForm.limitstart.value=".$base.";document.adminForm.submit();\">".$item->text."</a>";
		}
		else {
			return parent::_item_active($item);
		}
	}

	function _item_inactive(&$item)
	{
		if ($this->asForm){
			$base = $item->base>0 ? $item->base:0;
			//return "<input type='button' disabled=\"disabled\" title=\"".$item->text."\" onclick=\"this.form.limitstart.value=$base;submitform();return false;\" class=\"pagenav\" value=\"$item->text\" />";
			return "<span>".$item->text."</span>";
		}
		else {
			return parent::_item_inactive($item);
		}
	}



}