<?php

// no direct access
defined('_JEXEC') or die;

class ApiTableLog extends JTable
{
	
	var $id			= null;
	var $hash		= null;
	var $ip_address	= null;
	var $time		= null;
	var $request	= null;

	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__api_logs', 'id', $db);
	}
}