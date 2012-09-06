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

class TownwizardModelPartnerLocation extends TownwizardModel
{
    protected $_tableAlias = 'pl';

    public function __construct()
    {
        parent::__construct();

        $this->_query['joins'][] = 'INNER JOIN #__townwizard_partner p ON pl.partner_id = p.id';
        $this->_query['fields'][] = 'p.name as partner';
        $this->_query['order'] = 'pl.partner_id';
    }
}
?>