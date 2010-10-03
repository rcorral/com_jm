<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.model');

class ApiModel extends JModel {
	
	public function __construct($config=array) {
		parent::__construct($config);
	}
}