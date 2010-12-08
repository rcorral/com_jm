<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerHttp extends ApiController {
	
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	public function display() {
		$this->resetDocumentType();
		$auth	= JModel::getInstance('Authentication', 'ApiModel');
		
		$user	= $auth->authenticateRequest();
		
		if ($user === false) :
			ApiError::raiseError(403, $auth->getError());
		endif;
		
		jimport('joomla.plugin.helper');
		$name		= JRequest::getCmd('app');
		$handler	= ApiPlugin::getInstance($name);
		$handler->set('user', $user);
		$method		= $handler->get('method');
		
		if (!method_exists($handler, $method)) :
			ApiError::raiseError(404, JText::_('API_PLUGIN_METHOD_NOT_FOUND'));
		endif;
		
		if (!is_callable(array($handler, $method))) :
			ApiError::raiseError(404, JText::_('API_PLUGIN_METHOD_NOT_CALLABLE'));
		endif;
		
		$response 	= $handler->$method();
		$output		= $handler->encode();
		
		echo $output;
	}
	
}