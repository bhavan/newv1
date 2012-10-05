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

$isNew        = ($this->partnerSection->id < 1);
$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Partner Section' ).': <small><small>[ ' . $text.' ]</small></small>' );
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
                    <p>If not specified then default section name will be used</p>
                    <input class="text_area" type="text" name="name" id="name" size="32" maxlength="120" value="<?php echo $this->partnerSection->name;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'name');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="url">
                        <?php echo JText::_( 'Url' ); ?>:
                    </label>
                </td>
                <td>
                    <p>If not specified then default section url will be used</p>
                    <input class="text_area" type="text" name="url" id="url" size="32" maxlength="255" value="<?php echo $this->partnerSection->url;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'url');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="json_api_url">
                        <?php echo JText::_( 'JSON API Url' ); ?>:
                    </label>
                </td>
                <td>
                    <p>If not specified then default section JSON API url will be used</p>
                    <input class="text_area" type="text" name="json_api_url" id="json_api_url" size="32" maxlength="255" value="<?php echo $this->partnerSection->json_api_url;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'json_api_url');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key24">
                    <label for="image">
                        <?php echo JText::_( "Image" ); ?>:
                    </label>
                </td>
                <td>
                        <?php
                            if ($this->partnerSection->image && $this->partnerSection->id)
                            {
                                echo '<img src="/media/com_townwizard/images/sections/' . $this->partnerSection->image . '" width="50"  alt="' . $this->partnerSection->image . '" border="0"><br />';
                            }
                        ?>
                        <p>If not specified then default section icon will be used</p>
                        <input type="file" name="image" id="image" ACCEPT="image/jpeg,image/png,image/jpg">
                        <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'image');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="partner_id">
                        <?php echo JText::_( 'Partner' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['partner_id'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'partner_id');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="section_id">
                        <?php echo JText::_( 'Section' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['section_id'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'section_id');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="parent_id">
                        <?php echo JText::_( 'Parent' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['parent_id'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'parent_id');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="ordering">
                        <?php echo JText::_( 'Ordering' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['ordering'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'ordering');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="ui_type">
                        <?php echo JText::_( 'UI Type' ); ?>:
                    </label>
                    <p>
                        WebView by default. Choose any other value if you sure that partner's site support this type of UI.
                    </p>
                </td>
                <td>
                    <?php echo $this->lists['ui_type'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partnerSection, 'ui_type');?>
                </td>
            </tr>
        </table>
    </div>

    <div class="clr"></div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="controller" value="partnersection" />
<input type="hidden" name="id" value="<?php echo $this->partnerSection->id; ?>" />
<input type="hidden" name="task" value="edit" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
?>
