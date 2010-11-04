<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class ApiPlugin extends JObject {
	
	protected $params		= null;
	protected $format		= null;
	protected $response		= null;
	protected $request		= null;
	
	static	$instances		= array();
	static	$plg_prefix		= 'plgAPI';
	static	$plg_path		= '/plugins/api/';
	
	public static function getInstance($request) {
		$component	= strtolower($request['app']);
		
		if (isset(self::$instances[$component])) :
			return self::$instances[$component];
		endif;
		
		$plugin	= JPluginHelper::getPlugin('api', $component);

		if (empty($plugin)) :
			JError::raiseError(400, JText::_('API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		jimport('joomla.filesystem.file');

		$plgfile	= JPATH_BASE.self::$plg_path.$component.'.php';

		if (!JFile::exists($plgfile)) :
			JError::raiseError(400, JText::_('API_FILE_NOT_FOUND'));
		endif;

		include_once $plgfile;
		
		$class 	= self::$plg_prefix.ucwords($component);

		if (!class_exists($class)) :
			JError::raiseError(400, JText::_('API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		$handler	=  new $class($request, $plugin->params);
		
		
		self::$instances[$component] = $handler;
		
		return self::$instances[$component];
	}
	
	public function __construct($request=array(), $params=null)
	{
		$this->set('params', new JParameter($params));
		
		$this->set('component', $request['app']);
		$this->set('method', $request['method']);
		$this->set('format', $request['output']);
		
		unset($request['app']);
		unset($request['method']);
		unset($request['output']);
		
		$this->set('request', $request);
	}
	
	public function __call($name, $arguments) {
		JError::raiseError(400, JText::_('API_PLUGIN_METHOD_UNREACHABLE'));
	}
	
	protected function setResponse($data) {
		$this->set('response', $data);
	}
	
	public function encode() {
		$format = $this->format;
		
		if ($format == 'xml') :
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