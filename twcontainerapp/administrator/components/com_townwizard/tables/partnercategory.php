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


class TablePartnerCategory extends TownwizardTable
{
    public $id = null;

    public $title = null;

	public function __construct(&$db)
    {
		parent::__construct('#__townwizard_partner_category', 'id', $db);
	}

    protected $_validationRules = array(
        array('required', 'title'),
        array('maxlength', 'title', array('maxlength' => 120))
    );
}
?>