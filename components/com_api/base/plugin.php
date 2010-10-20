<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class ApiPlugin extends JObject {
	
	protected $format		= null;
	protected $response		= null;
	protected $request		= null;
	
	public function __construct($params, $request=array())
	{
		$this->set('format', $params['format']);
		$this->set('request', $request);
	}
	
	public function __call($name, $arguments) {
		JError::raiseError(400, JText::_('API_PLUGIN_METHOD_UNREACHABLE'));
	}
	
	public function encode() {
		$format = $this->get('format');
	}
	
	protected function toJSON() {
		return json_encode($this->get('response'));
	}
	
	protected function toXML() {
		return;
	}
	
}