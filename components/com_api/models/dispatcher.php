<?php 
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiModelDispatcher extends ApiModel {
	
	protected $plugin_path	= null;
	
  	public function __construct($config=array()) {
    	parent::__construct($config);

		$this->set('plugin_path', JPATH_SITE.'/plugins/api');
  	}

	public function dispatch($params, $request) {
		jimport('joomla.filesystem.file');
		$plgfile	= $this->get('plugin_path').'/'.strtolower($params['component']).'.php';
		
		if (!JFile::exists($plgfile)) :
			JError::raiseError(400, JText::_('API_FILE_NOT_FOUND'));
		endif;
		
		include_once $plgfile;
		
		$class 	= 'plgAPI'.ucwords($params['component']);
		
		if (!class_exists($class)) :
			JError::raiseError(400, JText::_('API_PLUGIN_CLASS_NOT_FOUND'));
		endif;
		
		$handler	= new $class($params, $request);
		$method		= $params['method'];
		
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