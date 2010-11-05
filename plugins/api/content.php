<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgAPIContent extends ApiPlugin {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function articles() {

		$db	= JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__content LIMIT 10');
		$articles	= $db->loadObjectList();
		$this->setResponse($articles);
	}
	
}