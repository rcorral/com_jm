<?php 

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class ApiViewAccount extends ApiView {
	
	function display($tpl = null) {
		if ($this->routeLayout($tpl)) :
			return;
		endif;
	
		$auth	= JModel::getInstance('Authentication', 'ApiModel');
		//$auth->generateToken();
		
		parent::display($tpl);
	}	
	
		
}