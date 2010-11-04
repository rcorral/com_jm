<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerMain extends ApiController {
	
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	public function display() {
		$caching	= false;
		parent::display($caching);
	}
	
	public function dispatch() {
		$this->resetDocumentType();
		$request				= JRequest::get('METHOD');
		$output					= JModel::getInstance('Dispatcher', 'ApiModel')->dispatch($request);
		echo $output;
	}
	
}