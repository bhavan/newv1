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

$isNew        = ($this->category->id < 1);

$text = $isNew ? JText::_( 'New' ) : JText::_( 'Edit' );
JToolBarHelper::title(   JText::_( 'Partner Category' ).': <small><small>[ ' . $text.' ]</small></small>' );
JToolBarHelper::save('edit');
if ($isNew)  {
    JToolBarHelper::cancel();
} else {
    // for existing items the button is renamed `close`
    JToolBarHelper::cancel( 'cancel', 'Close' );
}
?>

<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="col100">
        <table class="admintable">
            <tr>
                <td width="100" align="right" class="key">
                    <label for="title">
                        <?php echo JText::_( 'Title' ); ?>:
                    </label>
                </td>
                <td>
                    <input class="text_area" type="text" name="title" id="title" size="32" maxlength="120" value="<?php echo $this->category->title;?>" />
                    <?php echo TownwizardHelper::getFieldErrors($this->category, 'title');?>
                </td>
            </tr>
        </table>
    </div>

    <div class="clr"></div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="controller" value="partnercategory" />
<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
<input type="hidden" name="task" value="edit" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php
?>