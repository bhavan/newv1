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

JToolBarHelper::title( JText::_( 'Section Manager' ), 'generic.png' );
JToolBarHelper::deleteList();
JToolBarHelper::editListX();
JToolBarHelper::addNewX();

?>

<form action="index.php" method="post" name="adminForm">
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
                <?php echo JText::_( 'Is default' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Default URL' ); ?>
            </th>
            <th class="title">
                <?php echo JText::_( 'Default JSON API URL' ); ?>
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
                $link = JRoute::_( 'index.php?option=com_townwizard&controller=section&task=edit&cid[]='. $row->id );

                ?>
                <tr class="<?php echo "row{$k}"; ?>">
                    <td>
                        <?php echo $this->pagination->getRowOffset($i); ?>
                    </td>
                    <td>
                      <?php echo $checked; ?>
                    </td>
                    <td>
                        <a href="<?php echo $link; ?>"><?php echo $row->name; ?></a>
                    </td>
                    <td>
                        <?php echo ($row->is_default ? 'Yes' : 'No'); ?>
                    </td>
                    <td>
                        <?php echo $row->default_url; ?>
                    </td>
                    <td>
                        <?php echo $row->default_json_api_url; ?>
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
                    <td colspan="6" align="center">Sections not found</td>
                </tr>
                <?php
            }
        ?>
    </tbody>
    </table>
</div>

<input type="hidden" name="option" value="com_townwizard" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="section" />

</form>
