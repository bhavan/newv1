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

$isNew        = ($this->section->id < 1);

$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Section' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::save('edit');
if ($isNew)  {
    JToolBarHelper::cancel();
} else {
    // for existing items the button is renamed `close`
    JToolBarHelper::cancel( 'cancel', 'Close' );
}
?>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
    <div class="col100">
        <table class="admintable">
            <tr>
                <td width="100" align="right" class="key">
                    <label for="name">
                        <?php echo JText::_( 'Name' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="name" id="name" size="32" maxlength="120" value="<?php echo $this->section->name;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->section, 'name');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="default_url">
                        <?php echo JText::_( 'Default URL' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="default_url" id="default_url" size="32" maxlength="255" value="<?php echo $this->section->default_url;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->section, 'default_url');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="default_json_api_url">
                        <?php echo JText::_( 'Default JSON API URL' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="default_json_api_url" id="default_json_api_url" size="32" maxlength="255" value="<?php echo $this->section->default_json_api_url;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->section, 'default_json_api_url');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="is_default">
                        <?php echo JText::_( 'Is default section' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['is_default'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->section, 'is_default');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key24">
                    <label for="default_image">
                        <?php echo JText::_( "Image" ); ?>:
                    </label>
                </td>
                <td>
                        <?php
                            if ($this->section->default_image && $this->section->id)
                            {
                                echo '<img src="/media/com_townwizard/images/sections/' . $this->section->default_image . '" width="50"  alt="' . $this->section->default_image . '" border="0"><br />';
                            }
                        ?>
                        <input type="file" name="default_image" id="default_image" ACCEPT="image/jpeg,image/png,image/jpg">
                        <?php echo TownwizardHelper::getFieldErrors($this->section, 'default_image');?>
                </td>
            </tr>


        </table>
    </div>

    <div class="clr"></div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="controller" value="section" />
<input type="hidden" name="id" value="<?php echo $this->section->id; ?>" />
<input type="hidden" name="task" value="edit" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
?>
