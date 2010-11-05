<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class ApiPlugin extends JObject {
	
	protected $user				= null;
	protected $params			= null;
	protected $format			= null;
	protected $response			= null;
	protected $request			= null;
	protected $request_method	= null;
	
	static	$instances		= array();
	static	$plg_prefix		= 'plgAPI';
	static	$plg_path		= '/plugins/api/';
	
	public static function getInstance($name) {
		
		if (isset(self::$instances[$name])) :
			return self::$instances[$name];
		endif;
		
		$plugin	= JPluginHelper::getPlugin('api', $name);

		if (empty($plugin)) :
			JError::raiseError(400, JText::_('API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		jimport('joomla.filesystem.file');

		$plgfile	= JPATH_BASE.self::$plg_path.$name.'.php';

		if (!JFile::exists($plgfile)) :
			JError::raiseError(400, JText::_('API_FILE_NOT_FOUND'));
		endif;

		include_once $plgfile;
		
		$class 	= self::$plg_prefix.ucwords($name);

		if (!class_exists($class)) :
			JError::raiseError(400, JText::_('API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		$handler	=  new $class($plugin->params);	
		
		self::$instances[$name] = $handler;
		
		return self::$instances[$name];
	}
	
	public function __construct($params=null)
	{
		$this->set('params', new JParameter($params));
		$this->set('component', JRequest::getCmd('app'));
		$this->set('method', JRequest::getCmd('method'));
		$this->set('format', JRequest::getCmd('output'));
		$this->set('request_method', JRequest::getMethod());
	}
	
	public function __call($name, $arguments) {
		JError::raiseError(400, JText::_('API_PLUGIN_METHOD_UNREACHABLE'));
	}
	
	protected function setResponse($data) {
		$this->set('response', $data);
	}
	
	public function encode() {
		if ($this->format == 'xml') :
			return $this->toXML();
		else :
			return $this->toJSON();
		endif;	
	}
	
	protected function toJSON() {
		return json_encode($this->get('response'));
	}
	
	protected function toXML() {
		return;
	}
	
}