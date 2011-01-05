<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerHttp extends ApiController {
	
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	public function display() {
		$this->resetDocumentType();
		jimport('joomla.plugin.helper');
		$name		= JRequest::getCmd('app');
		$handler	= ApiPlugin::getInstance($name);
		$output	= $handler->fetchResource();
		echo $output;
	}
	
}