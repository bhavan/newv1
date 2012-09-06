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

$isNew        = ($this->partner->id < 1);

$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );

JToolBarHelper::title(   JText::_( 'Partner' ).': <small><small>[ ' . $text.' ]</small></small>' );
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
                    <input class="text_area" type="text" name="name" id="name" size="32" maxlength="120" value="<?php echo $this->partner->name;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'name');?>
                </td>
            </tr>
            <tr>
                <td width="100" align="right" class="key">
                    <label for="itunes_app_id">
                        <?php echo JText::_( 'iTunes App id' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="itunes_app_id" id="itunes_app_id" size="32" maxlength="120" value="<?php echo $this->partner->itunes_app_id;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'itunes_app_id');?>
                </td>
            </tr>
            <tr>
                <td width="100" align="right" class="key">
                    <label for="android_app_id">
                        <?php echo JText::_( 'Android App id' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="android_app_id" id="android_app_id" size="32" maxlength="120" value="<?php echo $this->partner->android_app_id;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'android_app_id');?>
                </td>
            </tr>
            <tr>
                <td width="100" align="right" class="key">
                    <label for="facebook_app_id">
                        <?php echo JText::_( 'Facebook App id' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="facebook_app_id" id="facebook_app_id" size="32" maxlength="120" value="<?php echo $this->partner->facebook_app_id;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'facebook_app_id');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="partner_category_id">
                        <?php echo JText::_( 'Partner Category' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['partner_category_id'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'partner_category_id');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="phone_number">
                        <?php echo JText::_( 'Phone number' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="phone_number" id="phone_number" size="32" maxlength="30" value="<?php echo $this->partner->phone_number;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'country');?>
                </td>
            </tr>

            <tr>
                <td width="100" align="right" class="key">
                    <label for="website_url">
                        <?php echo JText::_( 'Website URL' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="website_url" id="website_url" size="32" maxlength="50" value="<?php echo $this->partner->website_url;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'website_url');?>
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
                            if ($this->partner->image && $this->partner->id)
                            {
                                echo '<img src="/media/com_townwizard/images/partners/' . $this->partner->image . '" width="320"  alt="' . $this->partner->image . '" border="0"><br />';
                            }
                        ?>
                        <input type="file" name="image" id="image" ACCEPT="image/jpeg,image/png,image/jpg">
                        <?php echo TownwizardHelper::getFieldErrors($this->partner, 'image');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="published">
                        <?php echo JText::_( 'Published' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['published'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'published');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="featured_partner">
                        <?php echo JText::_( 'Featured Partner' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['featured_partner'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'featured_partner');?>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <label for="priority">
                        <?php echo JText::_( 'Priority/Featured Level' ); ?>:
                    </label>
                </td>
                <td>
                    <?php echo $this->lists['priority'];?>
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'priority');?>
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
                    <?php echo TownwizardHelper::getFieldErrors($this->partner, 'ordering');?>
                </td>
            </tr>
        </table>
    </div>

    <div class="clr"></div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="controller" value="partner" />
<input type="hidden" name="id" value="<?php echo $this->partner->id; ?>" />
<input type="hidden" name="creator_id" value="<?php echo JFactory::getUser()->id;?>" />
<input type="hidden" name="task" value="edit" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
?>
