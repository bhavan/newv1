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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.model' );

class TownwizardModel extends Jmodel
{
    private $_id;
    private $_data;
    private $_row;
    private $_table;
    protected $_query;
    protected $_tableAlias = 'm';

	public function __construct()
    {
		parent::__construct();

        $array = JRequest::getVar('cid',  0, '', 'array');
        $this->setId($array[0]);

        $table = $this->getTable();
        $fields = array_keys($table->getProperties());
        foreach ($fields as $key => $field)
        {
            $fields[$key] = $this->_tableAlias . '.' . $field;
        }

        $this->_query = array(
            'fields' => $fields,
            'table' => $table->getTableName(),
            'conditions' => array(),
            'joins' => array(),
            'order' => '',
            'limit' => '',
            'offset' => '',
            'group' => ''
        );
	}

    public function getRow()
    {
        return $this->_row;
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function setQuery($query)
    {
        $this->_query = $query;
    }

    public function setId($id)
    {
        // Set id and wipe data
        $this->_id        = (int) $id;
        $this->_data    = null;
    }

    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns the query
     * @return string The query to be used to retrieve the rows from the database
     */
    function buildQuery(array $query=array())
    {
        if (!count($query))
        {
            $query = $this->_query;
        }

        $query['conditions'] = is_array($query['conditions']) ? $query['conditions'] : array($query['conditions']);
        $query['conditions'] = implode(' AND ', $query['conditions']);

        return $this->renderStatement(array(
        			'conditions' => ($query['conditions'] ? 'WHERE ' . $query['conditions'] : ''),
        			'fields' => implode(', ', $query['fields']),
        			'table' => $query['table'],
        			'alias' => $this->_tableAlias,
        			'order' => ($query['order'] ? 'ORDER BY ' . $query['order'] : ''),
        			'limit' => (is_numeric($query['limit']) && is_numeric($query['offset'])) ? sprintf('LIMIT %s, %s', $query['offset'], $query['limit']) : '',
        			'joins' => implode(' ', $query['joins']),
        			'group' => ($query['group'] ? 'GROUP BY ' . $query['group'] : '')
        		));
    }

    public function renderStatement(array $query)
    {
        extract($query);
        return "SELECT {$fields} FROM {$table} {$alias} {$joins} {$conditions} {$group} {$order} {$limit}";
    }

    /**
     * Retrieves the list of data
     * @return array Array of objects containing the data from the database
     */
    public function getList($limitstart=0, $limit=0)
    {
        $query = $this->buildQuery();
        $this->_data = $this->_getList($query, $limitstart, $limit);

        return $this->_data;
    }

    /**
     * Method to get a one object
     * @return object with data
     */
    public function getOne($id=null)
    {
        $object = null;
        if ($id || $this->_id)
        {
            $db =& JFactory::getDBO();

            $dbQuery = $this->getQuery();
            $idFieldName = ($this->_tableAlias ? $this->_tableAlias . '.' : '') . $this->getTable()->getKeyName();
            $dbQuery['conditions'][] = $idFieldName . " = '" . intval(($id ? $id : $this->_id)) . "'";
            $db->setQuery($this->buildQuery($dbQuery));

            $object = $db->loadObject();
        }

        if (!$object)
        {
            $object = $this->getTable();
        }

        return $object;
    }

    /**
     * Method to store a record
     *
     * @access    public
     * @return    boolean    True on success
     */
    function store()
    {
        $this->_row =& $this->getTable();

        $data = JRequest::get('post');
        //print_r($data);
        if (!$this->_row->bind($data))
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }
        //print_r($this->_row);
        if (!$this->_row->check())
        {
            $this->setError(implode('<br/>', $this->_row->getErrors()));

            foreach ($this->_row->getProperties() as $property => $value)
            {
                $this->_row->$property = htmlspecialchars($value);
            }

            return false;
        }

        foreach ($this->_row->getProperties() as $property => $value)
        {
            $this->_row->$property = htmlspecialchars(mysql_real_escape_string($value));
        }

        if (!$this->_row->store())
        {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        return true;
    }

    /**
     * Method to delete record(s)
     *
     * @access    public
     * @return    boolean    True on success
     */
    function delete()
    {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );
        $row =& $this->getTable();

        foreach($cids as $cid)
        {
            if (!$row->delete( $cid ))
            {
                $this->setError( $row->getErrorMsg() );
                return false;
            }
        }

        return true;
    }
}
?>