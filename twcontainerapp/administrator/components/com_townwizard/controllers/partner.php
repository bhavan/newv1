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

class TownwizardControllerPartner extends TownwizardController
{
    protected $_viewName = 'partner';
    protected $_defaultModelName = 'Partner';

    /**
     * constructor (registers additional tasks to methods)
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // Register Extra tasks
        $this->registerTask( 'add', 'edit' );
        $this->registerTask( 'unpublish', 'publish' );
    }

    /**
     * display the edit form
     * @return void
     */
    private function _renderForm($partner=null)
    {
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        if ($partner)
        {
            $partner = $partner->getRow();
        }
        else
        {
            $partner        =& $this->getModel()->getOne();
        }

        $category = $this->getModel('PartnerCategory');
        $categories = $category->getList();

        $categorieslist[]		= JHTML::_('select.option',  '', JText::_( 'Select Partner Category' ), 'id', 'title', true );
        $categorieslist			= array_merge( $categorieslist, $categories );
        $lists['partner_category_id']		= JHTML::_('select.genericlist', $categorieslist, 'partner_category_id',
                                                       'class="inputbox" size="1"', 'id', 'title',
                                                       $partner->partner_category_id, 'partner_category_id' );

        $lists['published'] = JHTML::_('select.booleanlist',  'published', '', $partner->published );
        $lists['featured_partner'] = JHTML::_('select.booleanlist',  'featured_partner', '', $partner->featured_partner );

        $priority[] = JHTML::_('select.option', '0', '0');
        $priority[] = JHTML::_('select.option', '1', '1');
        $priority[] = JHTML::_('select.option', '2', '2');
        $priority[] = JHTML::_('select.option', '3', '3');
        $priority[] = JHTML::_('select.option', '4', '4');
        $priority[] = JHTML::_('select.option', '5', '5');
        $lists['priority'] = JHTML::_('select.genericlist',  $priority, 'priority', 'class="inputbox" size="1"',
                                      'value', 'text', $partner->priority, 'priority');

        // build the html select list for ordering
        $model = $this->getModel('Partner');
        $query = 'SELECT ordering AS value, name AS text'
        . ' FROM ' . $model->getTable()->getTableName()
        . ' ORDER BY ordering'
        ;
        if (!$partner->id < 1) {
            $lists['ordering'] 			= JHTML::_('list.specificordering',  $partner, $partner->id, $query );
        }
        else {
            $lists['ordering'] 			= JHTML::_('list.specificordering',  $partner, '', $query );
        }

        $this->assignRef('partner', $partner);
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
            $model->setId(JRequest::getVar('id', 0, '', 'int'));
            $fileName = $model->getOne()->image;

            JRequest::setVar('image', $fileName, 'POST');

            if ($model->store()) {
                $model->getRow()->reorder($model->getRow()->getReorderCondition());

                if (strlen($model->getRow()->image) > 0 && $fileName != $model->getRow()->image)
                {
                    $uploadPath = JPATH_SITE.DS.'media'.DS.'com_townwizard'.DS.'images'.DS.'partners';
                    $model->getRow()->uploadFile('image', $uploadPath);
                    $filePath = $uploadPath . DS . $fileName;

                    if (JFile::exists($filePath))
                    {
                        JFile::delete($filePath);
                    }

                    //Do image resize to 320x60 pixels size
                    //TownwizardHelper::resizeImage($uploadPath . DS . $model->getRow()->image, 320, 60);
                }

                //Adding default sections to this partner if they not added yet
                $section = $this->getModel('Section');
                $dbQuery = $section->getQuery();
                $dbQuery['fields'] = array('s.id');
                $dbQuery['conditions'] = 's.is_default = 1';
                $db =& JFactory::getDBO();
                $db->setQuery($section->buildQuery($dbQuery));
                $defaultSections = $db->loadResultArray();

                if ($defaultSections)
                {
                    $partnerSection = $this->getModel('PartnerSection');
                    $dbQuery = $partnerSection->getQuery();
                    $dbQuery['fields'] = array('DISTINCT ps.section_id');
                    $dbQuery['conditions'][] = 'ps.section_id IN(' . implode(',', $defaultSections) . ')';
                    $dbQuery['conditions'][] = 'ps.partner_id = ' . $model->getId();

                    $db->setQuery($partnerSection->buildQuery($dbQuery));
                    $partnerSections = $db->loadResultArray();

                    $unaddedSections = array_diff($defaultSections, $partnerSections);
                    $psTable = $partnerSection->getTable();
                    $partnerId = $model->getId();
                    foreach ($unaddedSections as $sectionId)
                    {
                        $psTable->bind(array('partner_id' => $partnerId, 'section_id' => $sectionId));
                        $psTable->store();
                    }
                }


                $msg = JText::_('Partner Saved!');
                $link = 'index.php?option=com_townwizard&controller=partner';

                $this->setRedirect($link, $msg);
            } else {
                $mainframe->enqueueMessage(JText::_('Cannot save the Partner information'), 'error');
                $this->_renderForm($model);
            }
        }
        else
        {
            $this->_renderForm();
        }
    }

    protected function _prepareFilters(&$dbQuery)
    {
        $db =& JFactory::getDBO();
        $filter_partner_category_id	= JRequest::getVar('filter_partner_category_id', 0, '', 'int');
        $search				= JRequest::getVar('search', '', '', 'string');
        if (strpos($search, '"') !== false) {
            $search = str_replace(array('=', '<'), '', $search);
        }

        $lists['search'] = JString::strtolower($search);

        $category = $this->getModel('PartnerCategory');
        $categories = $category->getList();

        $categorieslist[] = JHTML::_('select.option',  '', JText::_( 'Filter by Partner Category' ), 'id', 'title');
        $categorieslist	= array_merge( $categorieslist, $categories );
        $lists['filter_partner_category_id'] = JHTML::_(
                    'select.genericlist', $categorieslist, 'filter_partner_category_id',
                    'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'id', 'title',
                    $filter_partner_category_id, 'filter_partner_category_id'
        );

        if ($filter_partner_category_id)
        {
            $dbQuery['conditions'][] = 'p.partner_category_id = ' . $filter_partner_category_id;
        }

        if ($search)
        {
            $dbQuery['conditions'][] = '(LOWER( p.name ) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false ) .
                                       ' OR p.id = ' . (int) $search . ')';
        }

        $this->assignRef('lists', $lists);
    }

    /**
     * remove record(s)
     * @return void
     */
    function remove()
    {
        $model = $this->getModel();
        if(!$model->delete()) {
            $msg = JText::_('Error: One or More Partner Could not be Deleted');
        } else {
            $msg = JText::_('Partner(s) Deleted');
        }

        $this->setRedirect('index.php?option=com_townwizard&controller=partner', $msg);
    }
}
?>
