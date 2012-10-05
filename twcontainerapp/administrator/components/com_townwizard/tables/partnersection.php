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


class TablePartnerSection extends TownwizardTable
{
    public $id = null;

    public $name = null;

    public $image = null;

    public $section_id = null;

    public $partner_id = null;

    public $parent_id = null;

    public $url = null;

    public $json_api_url = null;

    public $ordering = null;

    public $ui_type = null;

    public static $ui_types = array(1 => 'webview', 2 => 'json');

    protected $_validationRules = array();

	public function __construct(&$db)
    {
		parent::__construct('#__townwizard_partner_section', 'id', $db);
        $this->_setValidationRules();
	}

    protected function _setValidationRules()
    {
        $this->_validationRules = array(
            array('required', 'section_id, partner_id'),
            array('maxlength', 'name', array('maxlength' => 120)),
            array('file', 'image', array('types' => 'jpg,jpeg,png,gif', 'mimes' => 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif')),
            array('maxlength', 'url, json_api_url', array('maxlength' => 255)),
            array('in', 'ui_type', array('range' => array_keys(self::$ui_types)))
        );
    }

    public function getReorderCondition()
    {
        return 'partner_id = ' . (int) $this->partner_id;
    }
}
?>
