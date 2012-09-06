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


class TablePartnerLocation extends TownwizardTable
{
    public $id = null;

    public $partner_id = null;

    public $street = null;

    public $city = null;

    public $state = null;

    public $country = null;

    public $zip = null;

    public $latitude = null;

    public $longitude = null;

    public $map_zoom = null;

    protected $_validationRules = array(
        array('required', 'partner_id, latitude, longitude'),
        array('maxlength', 'city, state, country', array('maxlength' => 50)),
        array('maxlength', 'zip', array('maxlength' => 10)),
        array('numeric', 'latitude, longitude', array('float' => true)),
        array('numeric', 'map_zoom')
    );

	public function __construct(&$db)
    {
		parent::__construct('#__townwizard_partner_location', 'id', $db);
	}
}
?>