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

class TownwizardHelper
{
	public static function getFieldErrors($model, $field)
    {
        if (is_object($model) && method_exists($model, 'getErrorsByField') && $errors = $model->getErrorsByField($field))
        {
            $errors = '<li>' . implode('</li><li>', $errors) . '</li>';
            return '<ul class="errors">' . $errors . '</ul>';
        }
    }

    public static function resizeImage($imagePath, $newWidth, $newHeight)
    {
        jimport('joomla.filesystem.file');

        if (JFile::exists($imagePath))
        {
            require_once 'image/Image.php';
            $image = new Image($imagePath);
            $image->resize($newWidth, $newHeight)->quality(75);
            $image->save();
        }
    }
}
?>