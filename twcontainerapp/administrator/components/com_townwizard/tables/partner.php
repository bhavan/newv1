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


class TablePartner extends TownwizardTable
{
    public $id = null;

    public $name = null;

    public $creator_id = null;

    public $itunes_app_id = null;

    public $facebook_app_id = null;

    public $android_app_id = null;

    public $partner_category_id = null;

    public $phone_number = null;

    public $website_url = null;

    public $image = null;

    public $published = null;

    public $priority = null;

    public $ordering = null;

    public $featured_partner = null;

    protected $_validationRules = array(
        array('required', 'name, creator_id, partner_category_id, website_url, published, featured_partner'),
        array('maxlength', 'name, itunes_app_id, android_app_id, website_url', array('maxlength' => 120)),
        array('maxlength', 'phone_number, facebook_app_id', array('maxlength' => 30)),
        array('url', 'website_url'),
        array('boolean', 'published, featured_partner'),
        array('numeric', 'facebook_app_id'),
        array('file', 'image', array('types' => 'jpg,jpeg,png,gif', 'mimes' => 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif'))
    );

	public function __construct(&$db)
    {
		parent::__construct('#__townwizard_partner', 'id', $db);
	}
}
?>