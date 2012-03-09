<?php
/* 
* Copyright (c) 2010 Ayoro SAS. All rights reserved. http://www.ayoro.com/
*
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2, or (at your option)
 * any later version.

 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with Analytics Asynchronous; see the file COPYING. If not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330, Boston,
 * MA 02111-1307, USA.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');

class plgSystemAnalyticsAsynchronous extends JPlugin
{
	function plgAnalyticsAsynchronous(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$this->_plugin = JPluginHelper::getPlugin( 'system', 'analyticsasynchronous' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	function onAfterRender()
	{
		global $mainframe;
		
		// skip if admin page 
		if($mainframe->isAdmin())
		{
			return;
		}

		// get params
		$top = $this->params->get( 'top_code', '' );
		$bottom = $this->params->get( 'bottom_code', '');

		$buffer = JResponse::getBody();

		// insert code below <body>
		$buffer = preg_replace ("/<body>/", "<body>".$top, $buffer); 

		// insert code above </body>
		$buffer = preg_replace ("/<\/body>/", $bottom."</body>", $buffer); 

		JResponse::setBody($buffer);
		
		return true;
	}
}
?>
