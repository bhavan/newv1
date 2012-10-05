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

class TownwizardControllerPartnerSection extends TownwizardController
{
    protected $_viewName = 'partnersection';
    protected $_defaultModelName = 'PartnerSection';

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

    private function _renderForm($partnerSection=null)
    {
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        if ($partnerSection)
        {
            $partnerSection = $partnerSection->getRow();
        }
        else
        {
            $partnerSection        =& $this->getModel()->getOne();
        }

        $section = $this->getModel('Section');
        $sections = $section->getList();

        $sectionslist[]		= JHTML::_('select.option',  '', JText::_( 'Select Section' ), 'id', 'name', true );
        $sectionslist			= array_merge( $sectionslist, $sections );
        $lists['section_id']		= JHTML::_('select.genericlist', $sectionslist, 'section_id',
                                                       'class="inputbox" size="1"', 'id', 'name',
                                                       $partnerSection->section_id, 'section_id' );

        $partner = $this->getModel('Partner');
        $partners = $partner->getList();

        $partnerslist[]		= JHTML::_('select.option',  '', JText::_( 'Select Partner' ), 'id', 'name', true );
        $partnerslist			= array_merge( $partnerslist, $partners );
        $lists['partner_id']		= JHTML::_('select.genericlist', $partnerslist, 'partner_id',
                                                       'class="inputbox" size="1"', 'id', 'name',
                                                       $partnerSection->partner_id, 'partner_id' );

        $parent = $this->getModel('PartnerSection');
        $dbQuery = $parent->getQuery();
        $dbQuery['conditions'][] = 'parent_id IS NULL OR parent_id = 0';
        $parent->setQuery($dbQuery);
        $parents = $parent->getList();

        $parentslist[]		= JHTML::_('select.option',  '', JText::_( 'Select Parent Partner Section' ), 'id', 'display_name');
        $parentslist			= array_merge( $parentslist, $parents );
        $lists['parent_id']		= JHTML::_('select.genericlist', $parentslist, 'parent_id',
                                                       'class="inputbox" size="1"', 'id', 'display_name',
                                                       $partnerSection->parent_id, 'parent_id' );

        // build the html select list for ordering
        $model = $this->getModel('PartnerSection');
        $dbQuery = $model->getQuery();
        $dbQuery['fields'] = array("IF (ps.name <> '', ps.name, s.name) as text", "ps.ordering as value");
        $dbQuery['conditions'][] = "ps.partner_id = '" . (int) $partnerSection->partner_id . "'";
        $query = $model->buildQuery($dbQuery);

        if (!$partnerSection->id < 1)
        {
            $lists['ordering'] 			= JHTML::_('list.specificordering',  $partnerSection, $partnerSection->id, $query );
        }
        else
        {
            $lists['ordering'] 			= JHTML::_('list.specificordering',  $partnerSection, '', $query );
        }

        $ui_types = array();
        foreach (TablePartnerSection::$ui_types as $key => $value)
        {
            $ui_types[] = JHTML::_('select.option', $key, $value);
        }
        $lists['ui_type'] = JHTML::_('select.genericlist',  $ui_types, 'ui_type', 'class="inputbox" size="1"',
                                      'value', 'text', $partnerSection->ui_type, 'priority');

        $this->assignRef('lists', $lists);
        $this->assignRef('partnerSection', $partnerSection);

        parent::display();
    }

    /**
     * save a record (and redirect to main page)
     * @return void
     */
    public function edit()
    {
        global $mainframe;

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']))
        {
            $model = $this->getModel('PartnerSection');
            $model->setId((int)$_POST['id']);
            $fileName = $model->getOne()->image;

            JRequest::setVar('image', $fileName, 'POST');

            if ($model->store()) {
                $model->getRow()->reorder($model->getRow()->getReorderCondition());

                if (strlen($model->getRow()->image) > 0 && $fileName != $model->getRow()->image)
                {
                    $uploadPath = JPATH_SITE.DS.'media'.DS.'com_townwizard'.DS.'images'.DS.'sections';
                    $model->getRow()->uploadFile('image', $uploadPath);
                    $filePath = $uploadPath . DS . $fileName;

                    if (JFile::exists($filePath))
                    {
                        JFile::delete($filePath);
                    }

                    //Do image resize to 100x100 pixels size
                    TownwizardHelper::resizeImage($uploadPath . DS . $model->getRow()->image, 100, 100);
                }

                $msg = JText::_("Partner's Section Saved!");
                $link = 'index.php?option=com_townwizard&controller=partnersection';
                $this->setRedirect($link, $msg);
            } else {
                $mainframe->enqueueMessage(JText::_('Cannot save the Partner Section information'), 'error');
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
        $model = $this->getModel('PartnerSection');
        if(!$model->delete()) {
            $msg = JText::_('Error: One or More Partner Section Could not be Deleted');
        } else {
            $msg = JText::_('Partner Section(s) Deleted');
        }

        $this->setRedirect('index.php?option=com_townwizard&controller=partnersection', $msg);
    }

    protected function _prepareFilters(&$dbQuery)
    {
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
            $dbQuery['conditions'][] = 'ps.partner_id = ' . $filter_partner_id;
        }

        $this->assignRef('lists', $lists);
    }
}
?>
