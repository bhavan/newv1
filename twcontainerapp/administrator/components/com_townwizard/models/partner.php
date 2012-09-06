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

class TownwizardModelPartner extends TownwizardModel
{
    protected $_tableAlias = 'p';

    public function __construct()
    {
        parent::__construct();

        $this->_query['joins'][] = 'INNER JOIN #__townwizard_partner_category pc ON p.partner_category_id = pc.id';
        $this->_query['fields'][] = 'pc.title as category';
        $this->_query['order'] = 'p.ordering';
    }
}
?>