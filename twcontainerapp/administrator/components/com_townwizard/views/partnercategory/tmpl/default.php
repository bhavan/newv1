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

JToolBarHelper::title( JText::_( 'Partner Category Manager' ), 'generic.png' );
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>

<form action="index.php" method="post" name="adminForm">
    <table>
        <tr>
            <td width="100%">
                <?php echo JText::_( 'Filter' ); ?>:
                <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="text_area" onchange="document.adminForm.submit();" title="<?php echo JText::_( 'Filter by title or enter section ID' );?>"/>
                <button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
                <button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
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
                <?php echo JText::_( 'Title' ); ?>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td colspan="3">
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
                $link = JRoute::_( 'index.php?option=com_townwizard&controller=partnercategory&task=edit&cid[]='. $row->id );

                ?>
                <tr class="<?php echo "row{$k}"; ?>">
                    <td>
                        <?php echo $this->pagination->getRowOffset($i); ?>
                    </td>
                    <td>
                      <?php echo $checked; ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->title; ?></a>
                    </td>
                </tr>
                <?php
                $k = 1 - $k;
            }

            if ($i == 0)
            {
                ?>
                <tr>
                    <td colspan="3" align="center">Categories not found</td>
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
<input type="hidden" name="controller" value="partnercategory" />

</form>