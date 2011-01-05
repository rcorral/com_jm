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
	protected $resource_acl		= array();
	
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
		$param_path = JPATH_BASE.self::$plg_path.$name.'.xml';

		if (!JFile::exists($plgfile)) :
			ApiError::raiseError(400, JText::_('COM_API_FILE_NOT_FOUND'));
		endif;

		include_once $plgfile;
		$class 	= self::$plg_prefix.ucwords($name);

		if (!class_exists($class)) :
			ApiError::raiseError(400, JText::_('COM_API_PLUGIN_CLASS_NOT_FOUND'));
		endif;

		$handler	=  new $class();
		
		$cparams	= JComponentHelper::getParams('com_api');
		$params		= new JParameter($plugin->params, $param_path);
		$cparams->merge($params);
		
		$handler->set('params', $cparams);
		$handler->set('component', JRequest::getCmd('app'));
		$handler->set('resource', JRequest::getCmd('resource'));
		$handler->set('format', JRequest::getCmd('output'));
		$handler->set('request_method', JRequest::getMethod());
		
		self::$instances[$name] = $handler;
		
		return self::$instances[$name];
	}
	
	public function __construct()
	{
		
	}
	
	public function __call($name, $arguments) {
		ApiError::raiseError(400, JText::_('COM_API_PLUGIN_METHOD_UNREACHABLE'));
	}
	
	public function setResourceAccess($resource, $access, $method='GET') {
		$method = strtoupper($method);
		
		$this->resource_acl[$resource][$method] = $access;
		return true;
	}
	
	public function getResourceAccess($resource, $method='GET', $returnParamsDefault=true) {
		$method = strtoupper($method);
		
		if (isset($this->resource_acl[$resource]) && isset($this->resource_acl[$resource][$method])) :
			return $this->resource_acl[$resource][$method];
		else :
			if ($returnParamsDefault) :
				return $this->params->get('resource_access');
			else :
				return false;
			endif;
		endif;
	}
	
	public function fetchResource($resource=null) {
		
		if ($resource == null) :
			$resource = $this->get('resource');
		endif;
		
		if (!method_exists($this, $resource)) :
			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_FOUND'));
		endif;
		
		if (!is_callable(array($this, $resource))) :
			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_CALLABLE'));
		endif;
		
		$access		= $this->getResourceAccess($resource, $this->request_method);
		
		if ($access == 'protected') :
			$user = APIAuthentication::authenticateRequest();
			if ($user === false) :
				ApiError::raiseError(403, APIAuthentication::getAuthError());
			endif;
			$this->set('user', $user);
		endif;
		
		call_user_func(array($this, $resource));
		$output		= $this->encode();
		return $output;
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
		
		$this->_toXMLRecursive($response, $xml);
		
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
			$this->_handleMultiDimensions($key, $value, $xml);
		endforeach;
	}
	
	private function _handleMultiDimensions($key, $value, &$xml) {
		if (is_array($value) || is_object($value)) :
			$node = $xml->addChild($key);
			$this->_toXMLRecursive($value, $node);
		else :
			$node = $xml->addChild($key, htmlspecialchars($value));
		endif;
	}
	
}