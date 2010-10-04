<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class ApiPlugin extends JObject {
	
	protected $format		= null;
	protected $response		= null;
	
	public function __construct($params)
	{
		$this->set('format', $params['format']);
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