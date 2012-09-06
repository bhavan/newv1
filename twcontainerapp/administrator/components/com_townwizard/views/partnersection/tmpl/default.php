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

JToolBarHelper::title( JText::_( 'Partner Section Manager' ), 'generic.png' );
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>

<form action="index.php" method="post" name="adminForm">
    <table>
        <tr>
            <td nowrap="nowrap">
                <?php
                echo $this->lists['filter_partner_id'];
                ?>
                <button onclick="this.form.getElementById('filter_partner_id').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
        </tr>
    </table>
<div id="editcell">
    <table class="adminlist">
    <thead>
        <tr>
            <th width="5">
                <?php echo JText::_( 'NUM' ); ?>
            </th>
            <th width="20">
              <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />
            </th>

            <th class="title">
                <?php echo JText::_( 'Partner' ); ?>
            </th>

            <th class="title">
                <?php echo JText::_( 'Section name' ); ?>
            </th>

            <th width="8%" nowrap="nowrap">
                <?php echo JText::_( 'Order' ); ?>
                <?php echo JHTML::_('grid.order',  $this->items ); ?>
            </th>

            <th width="5">
                <?php echo JText::_( 'ID' ); ?>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="6">
                <?php echo $this->pagination->getListFooter(); ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <?php
            $k = 0;
            for ($i=0, $n=count( $this->items ); $i < $n; $i++)
            {
                $row =& $this->items[$i];
                $checked    = JHTML::_( 'grid.id', $i, $row->id );
                $link = JRoute::_( 'index.php?option=com_townwizard&controller=partnersection&task=edit&cid[]='. $row->id );

                ?>
                <tr class="<?php echo "row{$k}"; ?>">
                    <td>
                        <?php echo $this->pagination->getRowOffset($i); ?>
                    </td>
                    <td>
                      <?php echo $checked; ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->partner; ?></a>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->display_name; ?></a>
                    </td>
                    <td class="order">
                        <span><?php echo $this->pagination->orderUpIcon( $i, ($row->partner_id == @$this->items[$i-1]->partner_id), 'orderup', 'Move Up', true); ?></span>
                        <span><?php echo $this->pagination->orderDownIcon( $i, $n, ($row->partner_id == @$this->items[$i+1]->partner_id), 'orderdown', 'Move Down', true ); ?></span>
                        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->id; ?></a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }

            if ($i == 0)
            {
                ?>
                <tr>
                    <td colspan="6" align="center">Partner sections not found</td>
                </tr>
                <?php
            }
        ?>
    </tbody>
    </table>
</div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="task" value="index" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="partnersection" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>