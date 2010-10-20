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
		
		$params					= array();
		$params['component']	= JRequest::getCmd('app', '');
		$params['method']		= JRequest::getCmd('method', '');
		$params['format']		= JRequest::getCmd('output', 'json');
		
		$request				= JRequest::get();
		
		$model					= JModel::getInstance('Dispatcher', 'ApiModel');
		$output					= $model->dispatch($params, $request);
		echo $output;
	}
	
}