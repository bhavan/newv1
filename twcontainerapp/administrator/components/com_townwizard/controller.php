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

class TownwizardController extends JController
{
    protected $_viewName = 'partner';
    protected $_defaultModelName = '';
    protected $_view = null;

	/**
	 * Custom Constructor
	 */
	public function __construct()
	{
        JRequest::setVar('view', $this->_viewName);

        parent::__construct();

        $this->registerTask('orderdown', 'order');
        $this->registerTask('orderup', 'order');
	}

    public function getDefaultModelName()
    {
        return $this->_defaultModelName;
    }

    public function getModel($name='', $prefix='', $config=array())
    {
        if (empty($name))
        {
            $name = $this->getDefaultModelName();
        }
        return parent::getModel($name, $prefix, $config);
    }

    protected function &_getView()
    {
        if (!$this->_view)
        {
            $document =& JFactory::getDocument();

            $viewType	= $document->getType();

            $this->_view = & $this->getView($this->_viewName, $viewType, '', array( 'base_path'=>$this->_basePath));
        }
        return $this->_view;
    }

    public function assignRef($key, $val)
    {
        return $this->_getView()->assignRef($key, $val);
    }

    /**
    * Publishes or Unpublishes one or more records
    * @param array An array of unique category id numbers
    * @param integer 0 if unpublishing, 1 if publishing
    * @param string The current url option
    */
    public function publish()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $db 	=& JFactory::getDBO();

        $cid		= JRequest::getVar( 'cid', array(), '', 'array' );
        $publish	= ( $this->getTask() == 'publish' ? 1 : 0 );

        JArrayHelper::toInteger($cid);

        if (count( $cid ) < 1)
        {
            $action = $publish ? 'publish' : 'unpublish';
            JError::raiseError(500, JText::_( 'Select an item to' .$action, true ) );
        }

        $cids = implode( ',', $cid );

        $query = 'UPDATE ' . $this->getModel()->getTable()->getTableName()
        . ' SET published = ' . (int) $publish
        . ' WHERE id IN ( '. $cids .' )'
        ;

        $db->setQuery( $query );
        if (!$db->query())
        {
            JError::raiseError(500, $db->getErrorMsg() );
        }

        $mainframe->redirect( 'index.php?option=com_townwizard&controller=' . $this->_viewName );
    }

    public function index()
    {
        global $mainframe, $option;
        $db =& JFactory::getDBO();

        $limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );

        $model = $this->getModel();

        $dbQuery = $model->getQuery();
        $fields = $dbQuery['fields'];
        $dbQuery['fields'] = array('COUNT(*)');

        $this->_prepareFilters($dbQuery);

        $db->setQuery($model->buildQuery($dbQuery));
        $total = $db->loadResult();

        jimport('joomla.html.pagination');
        $pagination = new JPagination($total, $limitstart, $limit);

        $dbQuery['fields'] = $fields;

        $model->setQuery($dbQuery);
        $items = $model->getList($pagination->limitstart, $pagination->limit);

        $this->assignRef('items', $items );
        $this->assignRef('pagination', $pagination );

        parent::display();
    }

    protected function _prepareFilters(&$dbQuery)
    {

    }

    /**
     * cancel editing a record
     * @return void
     */
    public function cancel()
    {
        $msg = JText::_( 'Operation Cancelled' );
        $this->setRedirect( 'index.php?option=com_townwizard&controller=' . $this->_viewName, $msg );
    }

    protected function order()
    {
        $this->_orderContent((JRequest::getVar('task') == 'orderdown' ? 1: -1));
    }

    /**
    * Moves the order of a record
    * @param integer The increment to reorder by
    */
    function _orderContent($direction)
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db		= & JFactory::getDBO();

        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

        if (isset( $cid[0] ))
        {
            $row = & $this->getModel()->getTable();
            $row->load( (int) $cid[0] );
            $row->move($direction, $row->getReorderCondition());

            $cache = & JFactory::getCache('com_townwizard');
            $cache->clean();
        }

        $mainframe->redirect('index.php?option=com_townwizard&controller=' . $this->_viewName);
    }

    public function saveOrder()
    {
        global $mainframe;

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        // Initialize variables
        $db			= & JFactory::getDBO();

        $cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $order		= JRequest::getVar( 'order', array (0), 'post', 'array' );
        $total		= count($cid);
        $conditions	= array ();

        JArrayHelper::toInteger($cid, array(0));
        JArrayHelper::toInteger($order, array(0));

        // Instantiate an article table object
        $row = & $this->getModel()->getTable();

        // Update the ordering for items in the cid array
        for ($i = 0; $i < $total; $i ++)
        {
            $row->load( (int) $cid[$i] );
            if ($row->ordering != $order[$i]) {
                $row->ordering = $order[$i];
                if (!$row->store()) {
                    JError::raiseError( 500, $db->getErrorMsg() );
                    return false;
                }
                // remember to updateOrder this group
                $condition = $row->getReorderCondition();
                $found = false;
                foreach ($conditions as $cond)
                    if ($cond[1] == $condition) {
                        $found = true;
                        break;
                    }
                if (!$found)
                    $conditions[] = array ($row->id, $condition);
            }
        }

        // execute updateOrder for each group
        foreach ($conditions as $cond)
        {
            $row->load($cond[0]);
            $row->reorder($cond[1]);
        }

        $cache = & JFactory::getCache('com_townwizard');
        $cache->clean();

        $msg = JText::_('New ordering saved');

        $mainframe->redirect('index.php?option=com_townwizard&controller='.$this->_viewName, $msg);
    }
}
?>