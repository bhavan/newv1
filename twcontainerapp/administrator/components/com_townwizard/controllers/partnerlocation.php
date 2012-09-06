<?php
/**
 * @version		1.0.0 townwizard_container $
 * @package		townwizard_container
 * @copyright	Copyright © 2012 - All rights reserved.
 * @license		GNU/GPL
 * @author		MLS
 * @author mail	nobody@nobody.com
 *
 *
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class TownwizardControllerPartnerLocation extends TownwizardController
{
    protected $_viewName = 'partnerlocation';
    protected $_defaultModelName = 'PartnerLocation';

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Register Extra tasks
        $this->registerTask( 'add', 'edit' );
    }

    /**
     * display the edit form
     * @return void
     */
    private function _renderForm($partnerLocation=null)
    {
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        if ($partnerLocation)
        {
            $partnerLocation = $partnerLocation->getRow();
        }
        else
        {
            $partnerLocation =& $this->getModel()->getOne();
        }

        $partner = $this->getModel('Partner');
        $partners = $partner->getList();

        $partnerslist[]		= JHTML::_('select.option',  '', JText::_( 'Select Partner' ), 'id', 'name', true );
        $partnerslist			= array_merge( $partnerslist, $partners );
        $lists['partner_id']		= JHTML::_('select.genericlist', $partnerslist, 'partner_id',
                                                       'class="inputbox" size="1"', 'id', 'name',
                                                       $partnerLocation->partner_id, 'partner_id' );

        $doc = & JFactory::getDocument();
        $doc->addScript('http://maps.googleapis.com/maps/api/js?sensor=false');//Add map api script
        $doc->addStyledeclaration("#sp_simple_map_canvas {margin:0;padding:0;height:300px}");

        $this->assignRef('partnerLocation', $partnerLocation);
        $this->assignRef('lists', $lists);

        parent::display();
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    function edit()
    {
        global $mainframe;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']))
        {
            $model = $this->getModel();

            if ($model->store()) {
                $msg = JText::_("Partner's Location Saved!");
                $link = 'index.php?option=com_townwizard&controller=partnerlocation';

                $this->setRedirect($link, $msg);
            } else {
                $mainframe->enqueueMessage(JText::_("Cannot save the Partner's location information"), 'error');
                $this->_renderForm($model);
            }
        }
        else
        {
            $this->_renderForm();
        }
    }

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel();
        if(!$model->delete()) {
            $msg = JText::_("Error: One or More Partner's Locations Could not be Deleted");
        } else {
            $msg = JText::_("Partner's Locations Deleted");
        }

        $this->setRedirect('index.php?option=com_townwizard&controller=partnerlocation', $msg);
    }

    protected function _prepareFilters(&$dbQuery)
    {
        $db =& JFactory::getDBO();

        $search				= JRequest::getVar('search', '', '', 'string');
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }

        $lists['search'] = JString::strtolower($search);

        $filter_partner_id	= JRequest::getVar('filter_partner_id', 0, '', 'int');

        $partner = $this->getModel('Partner');
        $partners = $partner->getList();

        $partnerslist[]		= JHTML::_('select.option',  '', JText::_( 'Filter by Partner' ), 'id', 'name');
        $partnerslist			= array_merge( $partnerslist, $partners);
        $lists['filter_partner_id']		= JHTML::_('select.genericlist', $partnerslist, 'filter_partner_id',
                                                       'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'id', 'name',
                                                        $filter_partner_id, 'filter_partner_id' );

        if ($filter_partner_id)
        {
            $dbQuery['conditions'][] = 'pl.partner_id = ' . $filter_partner_id;
        }

        if ($search)
        {
            $dbQuery['conditions'][] = '(LOWER( pl.city ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                                       ' OR LOWER( pl.zip ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) . ')';
        }

        $this->assignRef('lists', $lists);
    }

}
?>