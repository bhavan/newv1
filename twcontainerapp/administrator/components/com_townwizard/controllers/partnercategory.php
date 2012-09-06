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

class TownwizardControllerPartnerCategory extends TownwizardController
{
    protected $_viewName = 'partnercategory';
    protected $_defaultModelName = 'PartnerCategory';

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    function __construct()
    {
        parent::__construct();

        // Register Extra tasks
        $this->registerTask( 'add', 'edit' );
    }

    /**
     * display the edit form
     * @return void
     */
    private function _renderForm($partnerCategory=null)
    {
        JRequest::setVar( 'layout', 'form'  );
        JRequest::setVar( 'hidemainmenu', 1 );

        if ($partnerCategory)
        {
            $partnerCategory = $partnerCategory->getRow();
        }
        else
        {
            $partnerCategory        =& $this->getModel()->getOne();
        }

        $this->assignRef('category', $partnerCategory);

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
                $msg = JText::_( 'Partner category Saved!' );
                $link = 'index.php?option=com_townwizard&controller=' . $this->_viewName;
                $this->setRedirect($link, $msg);
            } else {
                $mainframe->enqueueMessage(JText::_('Cannot save the Partner Category information'), 'error');
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
        $model = $this->getModel('PartnerCategory');
        if(!$model->delete()) {
            $msg = JText::_( 'Error: One or More Partner Categories Could not be Deleted' );
        } else {
            $msg = JText::_( 'Partner(s) Deleted' );
        }

        $this->setRedirect( 'index.php?option=com_townwizard&controller=partnercategory', $msg );
    }

    protected function _prepareFilters(&$dbQuery)
    {
        $db =& JFactory::getDBO();
        $search				= JRequest::getVar('search', '', '', 'string');
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }

        $lists['search'] = JString::strtolower($search);

        if ($search)
        {
            $dbQuery['conditions'][] = '(LOWER( pc.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                                       ' OR pc.id = ' . (int) $search . ')';
        }

        $this->assignRef('lists', $lists);
    }
}
?>