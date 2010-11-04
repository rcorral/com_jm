<?php 
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiModelDispatcher extends ApiModel {
	
	protected $plugin_path	= null;
	
  	public function __construct($config=array()) {
    	parent::__construct($config);

  	}

	public function dispatch($request) {
		jimport('joomla.plugin.helper');
		
		$handler	= ApiPlugin::getInstance($request);
		$method		= $handler->get('method');
		
		if (!method_exists($handler, $method)) :
			JError::raiseError(400, JText::_('API_PLUGIN_METHOD_NOT_FOUND'));
		endif;
		
		if (!is_callable(array($handler, $method))) :
			JError::raiseError(400, JText::_('API_PLUGIN_METHOD_NOT_CALLABLE'));
		endif;
		
		$response 	= $handler->$method();
		$output		= $handler->encode();
		return $output;
	}

}