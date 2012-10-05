<?php
function TownwizardParseRoute($segments)
{
    $vars = array();

    // get a menu item based on Itemid or currently active
    $menu = &JSite::getMenu();
    if (empty($query['Itemid'])) {
        $menuItem = &$menu->getActive();
    } else {
        $menuItem = &$menu->getItem($query['Itemid']);
    }
    $layout = isset($menuItem->query['layout']) ? $menuItem->query['layout'] : '';

    switch ($segments[0])
    {
        case 'partner':
            {
                if (isset($segments[1]) && is_numeric($segments[1]))
                {
                    $vars['controller'] = $segments[0];
                    $vars['cid'] = $segments[1];
                    $vars['task'] = 'partner';
                }
                elseif (!isset($segments[1]))
                {
                    $vars['controller'] = $segments[0];
                    $vars['task'] = 'search';
                }
                break;
            }
        case 'section':
            {
                if (isset($segments[1]) && isset($segments[2]) && is_numeric($segments[2]) && $segments[1] == 'partner')
                {
                    $vars['cid'] = (int) $segments[2];
                    $vars['controller'] = $segments[0];
                    $vars['task'] = 'partner';
                }
                else if (isset($segments[1]) && is_numeric($segments[1]))
                {
                    $vars['cid'] = (int) $segments[1];
                    $vars['controller'] = $segments[0];
                    $vars['task'] = 'section';
                }
            }
    }

    if ($layout)
    {
        $vars['layout'] = $layout;
    }

    return $vars;
}