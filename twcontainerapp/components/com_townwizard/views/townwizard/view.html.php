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

jimport( 'joomla.application.component.view' );

class TownwizardViewTownwizard extends Jview
{
    public function display($tpl=null)
    {
        header("HTTP/1.0 404 Not Found");
        header("Content-type: application/json");
        echo json_encode(array('status' => 0, 'error' => 'Page not found'));
        exit();
    }
}
?>