<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
class TOOLBAR_pagemeta {
	function _NEW() {
		JToolBarHelper::title( JText::_( 'Page Meta' ).': <small><small>[ add ]</small></small>', 'generic.png' );		
		JToolBarHelper::save();		
		JToolBarHelper::cancel();
	}
	function _EDIT() {
		JToolBarHelper::title( JText::_( 'Page Meta' ).': <small><small>[ edit ]</small></small>', 'generic.png' );		
		JToolBarHelper::save();		
		JToolBarHelper::cancel();
	}
	function _GLOBAL() {
		JToolBarHelper::title( JText::_( 'Global Settings' ) , 'generic.png' );		
		JToolBarHelper::save();		
		JToolBarHelper::cancel();
	}
	function _DEFAULT() {
		JToolBarHelper::title( JText::_( 'Page Meta' ), 'generic.png' );			
		JToolBarHelper::custom('globalseting','config.png','config.png','Global',0,0);
		JToolBarHelper::editList();
		JToolBarHelper::deleteList();
		JToolBarHelper::addNew('add');
	}
}
?>