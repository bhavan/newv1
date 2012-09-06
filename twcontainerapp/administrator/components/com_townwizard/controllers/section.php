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

class TownwizardControllerSection extends TownwizardController
{
    protected $_viewName = 'section';
    protected $_defaultModelName = 'Section';

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
    private function _renderForm($section=null)
    {
        JRequest::setVar('layout', 'form');
        JRequest::setVar('hidemainmenu', 1);

        if ($section)
        {
            $section = $section->getRow();
        }
        else
        {
            $section        =& $this->getModel()->getOne();
        }

        $lists['is_default'] = JHTML::_('select.booleanlist',  'is_default', '', $section->is_default );

        $this->assignRef('section', $section);
        $this->assignRef('lists', $lists);

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
            $model = $this->getModel('Section');
            $model->setId((int)$_POST['id']);
            $fileName = $model->getOne()->default_image;

            JRequest::setVar('default_image', $fileName, 'POST');

            if ($model->store()) {
                if (strlen($model->getRow()->default_image) > 0 && $fileName != $model->getRow()->default_image)
                {
                    $uploadPath = JPATH_SITE.DS.'media'.DS.'com_townwizard'.DS.'images'.DS.'sections';
                    $model->getRow()->uploadFile('default_image', $uploadPath);
                    $filePath = $uploadPath . DS . $fileName;

                    if (JFile::exists($filePath))
                    {
                        JFile::delete($filePath);
                    }

                    //Do image resize to 50x50 pixels size
                    TownwizardHelper::resizeImage($uploadPath . DS . $model->getRow()->default_image, 100, 100);
                }

                $msg = JText::_('Section Saved!');
                $link = 'index.php?option=com_townwizard&controller=section';
                $this->setRedirect($link, $msg);
            } else {
                $mainframe->enqueueMessage(JText::_('Cannot save the Section information'), 'error');
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
    public function remove()
    {
        $model = $this->getModel('Section');
        if(!$model->delete()) {
            $msg = JText::_('Error: One or More Section Could not be Deleted');
        } else {
            $msg = JText::_('Section(s) Deleted');
        }

        $this->setRedirect('index.php?option=com_townwizard&controller=section', $msg);
    }
}
?>
