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


class TableSection extends TownwizardTable
{
    public $id = null;

    public $name = null;

    public $default_image = null;

    public $default_url = null;

    public $default_json_api_url = null;

    public $is_default = null;

    protected $_validationRules = array(
        array('required', 'name, default_url'),
        array('maxlength', 'name', array('maxlength' => 120)),
        array('maxlength', 'default_url, default_json_api_url', array('maxlength' => 255)),
        array('file', 'default_image', array('types' => 'jpg,jpeg,png,gif', 'mimes' => 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif')),
    );

	public function __construct(&$db)
    {
		parent::__construct('#__townwizard_section', 'id', $db);
	}
}
?>
