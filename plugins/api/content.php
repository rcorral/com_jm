<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Example User Plugin
 *
 * @package		Joomla
 * @subpackage	JFramework
 * @since 		1.5
 */
class plgAPIContent extends ApiPlugin {
	
	public function __construct($params=array(), $request=array())
	{
		parent::__construct($params, $request);
	}
	
	public function articles() {
		$db	= JFactory::getDBO();
		$db->setQuery('SELECT * FROM #__content LIMIT 10');
		$articles	= $db->loadObjectList();
		$this->setResponse($articles);
	}
	
}