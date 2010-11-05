<?php

// no direct access
defined('_JEXEC') or die;

class ApiTableToken extends JTable
{
	
	var $id			= null;
	var $user_id	= null;
	var $hash		= null;
	var $domain		= null;
	var $created	= null;
	var $created_by	= null;
	var $enabled	= null;

	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db)
	{
		parent::__construct('#__api_tokens', 'id', $db);
	}
}