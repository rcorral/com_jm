<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerMain extends ApiController {
	
	public function display() {
		echo 'test';
		return;
		$caching	= false;
		parent::display($caching);
	}
	
}