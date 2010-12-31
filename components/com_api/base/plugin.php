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
			ApiError::raiseError(400, JText::_('COM_API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		jimport('joomla.filesystem.file');

		$plgfile	= JPATH_BASE.self::$plg_path.$name.'.php';

		if (!JFile::exists($plgfile)) :
			ApiError::raiseError(400, JText::_('COM_API_FILE_NOT_FOUND'));
		endif;

		include_once $plgfile;
		
		$class 	= self::$plg_prefix.ucwords($name);

		if (!class_exists($class)) :
			ApiError::raiseError(400, JText::_('COM_API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		$handler	=  new $class($plugin->params);	
		
		self::$instances[$name] = $handler;
		
		return self::$instances[$name];
	}
	
	public function __construct($params=null)
	{
		$this->set('params', new JParameter($params));
		$this->set('component', JRequest::getCmd('app'));
		$this->set('resource', JRequest::getCmd('resource'));
		$this->set('format', JRequest::getCmd('output'));
		$this->set('request_method', JRequest::getMethod());
	}
	
	public function __call($name, $arguments) {
		ApiError::raiseError(400, JText::_('COM_API_PLUGIN_METHOD_UNREACHABLE'));
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
		//JResponse::setHeader('Content-Type', 'text/xml');
		//print_r($GLOBALS['_JRESPONSE']->headers);
		$response = $this->get('response');
		$xml = new SimpleXMLElement('<?xml version="1.0"?><response></response>');
		
		$this->_toXMLRecursive($response, &$xml);
		
		return $xml->asXML();
	}
	
	private function _toXMLRecursive($element, &$xml) {
		
		if (!is_array($element) && !is_object($element)) :
			return null;
		endif;
		
		if (is_object($element)) :
			$element = get_object_vars($element);
		endif;
		
		foreach($element as $key => $value) :
			$this->_handleMultiDimensions($key, $value, &$xml);
		endforeach;
	}
	
	private function _handleMultiDimensions($key, $value, &$xml) {
		if (is_array($value) || is_object($value)) :
			$node = $xml->addChild($key);
			$this->_toXMLRecursive($value, &$node);
		else :
			$node = $xml->addChild($key, htmlspecialchars($value));
		endif;
	}
	
}