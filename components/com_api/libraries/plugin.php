<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

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
	protected $cache_folder		= 'com_api';
	
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
	
	//public function __call($name, $arguments) {
	//	ApiError::raiseError(400, JText::_('COM_API_PLUGIN_METHOD_UNREACHABLE'));
	//}
	
	final public function setResourceAccess($resource, $access, $method='GET') {
		$method = strtoupper($method);
		
		$this->resource_acl[$resource][$method] = $access;
		return true;
	}
	
	final public function getResourceAccess($resource, $method='GET', $returnParamsDefault=true) {
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
	
	final public function fetchResource($resource_name=null) {
		
		if ($resource_name == null) :
			$resource_name = $this->get('resource');
		endif;
		
		$resource_obj = ApiResource::getInstance($resource_name, $this);
			
		if ($resource_obj === false) :
			$this->checkInternally($resource_name);
		endif;
		
		$access		= $this->getResourceAccess($resource_name, $this->request_method);
		
		if ($access == 'protected') :
			$user = APIAuthentication::authenticateRequest();
			if ($user === false) :
				ApiError::raiseError(403, APIAuthentication::getAuthError());
			endif;
			$this->set('user', $user);
		endif;
		
		if (!$this->checkRequestLimit()) :
			ApiError::raiseError(403, JText::_('COM_API_RATE_LIMIT_EXCEEDED'));
		endif;
		
		$this->log();
		
		
		if ($resource_obj !== false) :
			$resource_obj->invoke();
		else :
			call_user_func(array($this, $resource_name));
		endif;
		
		$output		= $this->encode();
		return $output;
	}
	
	final private function checkInternally($resource_name) {
		if (!method_exists($this, $resource_name)) :
			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_FOUND'));
		endif;

		if (!is_callable(array($this, $resource_name))) :
			ApiError::raiseError(404, JText::_('COM_API_PLUGIN_METHOD_NOT_CALLABLE'));
		endif;
		
		return true;
	}
	
	final private function checkRequestLimit() {
		$limit = $this->params->get('request_limit', 0);
		if ($limit == 0) :
			return true;
		endif;
		
		$hash = JRequest::getVar('key', '');
		$ip_address = JRequest::getVar('REMOTE_ADDR', '', 'server');
		
		$time = $this->params->get('request_limit_time', 'hour');
		switch($time) :
			case 'day':
			$offset = 60*60*24;
			break;
			
			case 'minute':
			$offset = 60;
			break;
			
			case 'hour':
			default:
			$offset = 60*60;
			break;
		endswitch;
		
		$query_time = time() - $offset;
		
		$db = JFactory::getDBO();
		$query = "SELECT COUNT(*) FROM #__api_logs "
				."WHERE `time` >= ".$db->Quote($query_time)." "
				."AND (`hash` = ".$db->Quote($hash)." OR `ip_address` = ".$db->Quote($ip_address).")"
				;
		
		$db->setQuery($query);
		$result = $db->loadResult();
		
		if ($result >= $limit) :
			return false;
		else :
			return true;
		endif;
		
	}
	
	final private function log() {
		$table = JTable::getInstance('Log', 'ApiTable');
		$table->hash = JRequest::getVar('key', '');
		$table->ip_address = JRequest::getVar('REMOTE_ADDR', '', 'server');
		$table->time = time();
		$table->request = JFactory::getURI()->getQuery();
		$table->store();
	}
	
	public function setResponse($data) {
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