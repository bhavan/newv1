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

JToolBarHelper::title( JText::_( 'Partner Manager' ), 'generic.png' );
//JToolBarHelper::deleteList();
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>

<form action="index.php" method="post" name="adminForm">
    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by title or enter article ID' );?>"/>
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_partner_category_id').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php
                echo $this->lists['filter_partner_category_id'];
                ?>
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
                <?php echo JText::_( 'Name' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Category' ); ?>
            </th>
            <th width="20">
                <?php echo JText::_( 'Published' ); ?>
            </th>
            <th width="8%" nowrap="nowrap">
                <?php echo JText::_( 'Order' ); ?>
                <?php echo JHTML::_('grid.order',  $this->items ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Priority' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'ID' ); ?>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="12">
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
                $link = JRoute::_( 'index.php?option=com_townwizard&controller=partner&task=edit&cid[]='. $row->id );
                $published 	= JHTML::_('grid.published', $row, $i );
                ?>
                <tr class="<?php echo "row{$k}"; ?>">
                    <td>
                        <?php echo $this->pagination->getRowOffset( $i ); ?>
                    </td>
                    <td>
                      <?php echo $checked; ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
                    </td>
                    <td>
                        <?php echo $row->category; ?>
                    </td>
                    <td>
                        <?php echo $published;?>
                    </td>
                    <td class="order">

                        <span><?php echo $this->pagination->orderUpIcon( $i, true, 'orderup', 'Move Up', true); ?></span>
                        <span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', true ); ?></span>

                        <input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
                    </td>
                    <td>
                        <?php echo $row->priority;?>
                    </td>
                    <td>
                        <?php echo $row->id;?>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }

            if ($i == 0)
            {
                ?>
                <tr>
                    <td colspan="12" align="center">Partners not found</td>
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
<input type="hidden" name="controller" value="partner" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>