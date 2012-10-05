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

class TownwizardModelPartnerSection extends TownwizardModel
{
    protected $_tableAlias = 'ps';

    public function __construct()
    {
        parent::__construct();

        $this->_query['joins'][] = 'INNER JOIN #__townwizard_section s ON ps.section_id = s.id';
        $this->_query['fields'][] = "IF (ps.name <> '', ps.name, s.name) as display_name";
        $this->_query['fields'][] = "IF (ps.image <> '', ps.image, s.default_image) as image_url";
        $this->_query['fields'][] = "IF (ps.url <> '', ps.url, s.default_url) as section_url";
        $this->_query['fields'][] = "IF (ps.json_api_url <> '', ps.json_api_url, s.default_json_api_url) as json_api_url";
        $this->_query['joins'][] = 'INNER JOIN #__townwizard_partner p ON ps.partner_id = p.id';
        $this->_query['fields'][] = 'p.name as partner';
        $this->_query['order'] = 'ps.partner_id, ps.ordering';
    }

    public function getChildren()
    {
        $children = array();
        if ($this->getId() > 0)
        {
            $dbQuery = $this->getQuery();
            $dbQuery['conditions'][] = 'ps.parent_id = ' . (int) $this->getId();
            $this->setQuery($dbQuery);
            $children = $this->getList();
        }

        return $children;
    }
/*
    public function getUiType()
    {
        $this->getTable()->getUiType($this->ui_type);
    }*/
}
?>