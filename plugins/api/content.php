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
		
		echo 'this is the content list function';
		
	}
	
}