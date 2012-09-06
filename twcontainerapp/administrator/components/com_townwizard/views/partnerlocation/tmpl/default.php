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

JToolBarHelper::title( JText::_( "Partner's Locations Manager" ), 'generic.png' );
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>

<form action="index.php" method="post" name="adminForm">
    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by city name or enter zip code' );?>"/>
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.getElementById('filter_partner_id').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
            </td>
            <td nowrap="nowrap">
                <?php
                echo $this->lists['filter_partner_id'];
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
                <?php echo JText::_( 'Partner' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Country' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'State/Province' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'City/Town' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Postcode/Zip' ); ?>
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
                $link = JRoute::_( 'index.php?option=com_townwizard&controller=partnerlocation&task=edit&cid[]='. $row->id );

                ?>
                <tr class="<?php echo "row{$k}"; ?>">
                    <td>
                        <?php echo $this->pagination->getRowOffset( $i ); ?>
                    </td>
                    <td>
                      <?php echo $checked; ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->partner; ?></a>
                    </td>
                    <td>
                        <?php echo $row->country;?>
                    </td>
                    <td>
                        <?php echo $row->state;?>
                    </td>
                    <td>
                        <?php echo $row->city;?>
                    </td>
                    <td>
                        <?php echo $row->zip;?>
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
                    <td colspan="12" align="center">Partner locations not found</td>
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
<input type="hidden" name="controller" value="partnerlocation" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>