<?php
/**
 * @version		$Id: helper.php 1028 2008-07-06 22:46:56Z Fritz Elfert $
 * @copyright	Copyright (C) 2008 Fritz Elfert. All rights reserved.
 * @license		GNU/GPLv2
 */

class modAvReloadedHelper {
    /**
     * Retrieves the actual video
     *
     * @param array $params An object containing the module parameters
     * @return The code for embedding the video
     * @access public
     */
    function getVideo($params, $mod) {
        $code = '';
        $res = null;
        $app = &JFactory::getApplication();
        if (JPluginHelper::importPlugin('content', 'avreloaded')) {
            $w = $params->get('pwidth', 320);
            $h = $params->get('pheight', 240);
            $header = $params->get('header_text', '');
            $footer = $params->get('footer_text', '');
            $plcode = preg_replace('#\s+#', ' ', $params->get('mediacode', ''));
            $plcode = modAvReloadedHelper::_removeParam('popup', $plcode);
            $popup = $params->get('popup', 0);
            switch ($popup) {
            case 0:
                $res = $app->triggerEvent('onAvReloadedGetVideo', array($plcode));
                // There should be exacty ONE return value in the result, because
                // this is a custom event type!
                if (is_array($res) && (count($res) == 1)) {
                    $code = $header.$res[0].$footer;
                }
                break;
            case 1:
            case 2:
                $plid = 'modavr_'.$mod->id;
                $plcode = modAvReloadedHelper::_injectParam('popup', 'true', $plcode);
                $plcode = modAvReloadedHelper::_injectParam('width', $w, $plcode);
                $plcode = modAvReloadedHelper::_injectParam('height', $h, $plcode);
                $res = $app->triggerEvent('onAvReloadedGetVideoAndID', array($plcode));
                // There should be exacty ONE return value in the result, because
                // this is a custom event type!
                if (is_array($res) && (count($res) == 1)) {
                    $linktxt = $params->get('linktxt', '');
                    $menus =& JSite::getMenu();
                    $amenu =& $menus->getActive();
                    $itemid = $amenu->id;

                    if ($linktxt != '') {
                        $plid = $res[0][1];
                        $code .= $header . $res[0][0] .
                            '<a href="#" onclick="return AvrPopup(event,'.
                            "'".$plid."','".
                            (($popup == 1)?'window':'lightbox').
                            "'".');">'.$linktxt.'</a>' .
                            $footer;
                    } else {
                        $code .= $header . $footer;
                    }
                }
                break;
            }
        }
        return $code;
    }

    function _injectParam($name, $val, $code) {
        $regex = '#\s+'.$name.'\s*=\s*"[^"]*"#';
        if (preg_match($regex, $code) == 0) {
            $code = preg_replace('/(}.*{)/',' '.$name.'="'.$val.'"$1', $code);
        }
        return $code;
    }

    function _removeParam($name, $code) {
        $regex = '#\s+'.$name.'\s*=\s*"[^"]*"#';
        $matches = null;
        if (preg_match_all($regex, $code, $matches, PREG_PATTERN_ORDER) > 0) {
            foreach ($matches[0] as $match) {
                $code = str_replace($match, ' ', $code);
            }
        }
        return $code;
    }

}
