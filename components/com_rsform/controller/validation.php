<?php
/**
* @version 1.2.0
* @package RSform!Pro 1.2.0
* @copyright (C) 2007-2009 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );


	//require_once('functions.php');
	function none($value,$extra=null)
	{
		return true;
	}
	
	function email($email,$extra=null)
	{
		$email = trim($email);
		if($email == '') return true;
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email))
		{
			return false;
		}
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++)
		{
			if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i]))
			{
			return false;
			}
		}
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1]))
		{
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2)
			{
			return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++)
			{
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i]))
				{
				return false;
				}
			}
		}
		return true;
	}
	function numeric($param,$extra=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
		{
			if(strpos($extra,$param[$i]) === FALSE && !is_numeric($param[$i])) return false;
		}
		return true;
	}
	
	function alphanumeric($param,$extra = null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
		{
			if(strpos($extra,$param[$i]) === FALSE && eregi('[^a-zA-Z0-9 ]', $param[$i])) return false;
		}
		return true;
	}
	
	function alpha($param,$extra=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
			
		for($i=0;$i<strlen($param);$i++)
		{
			if(strpos($extra,$param[$i]) === FALSE && eregi('[^a-zA-Z ]', $param[$i] )) return false;
		}
		return true;
	}
	
	function custom($param,$extra=null)
	{
		if(strpos($param,"\n") !== FALSE) 
			$param = str_replace(array("\r","\n"),'',$param);
		
		for($i=0;$i<strlen($param);$i++)
		{
			if(strpos($extra,$param[$i]) === FALSE) return false;
		}
		return true;
	}
	
	function password($param,$extra=null)
	{
		return true;
	}
?>