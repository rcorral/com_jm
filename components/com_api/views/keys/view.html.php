<?php 

// no direct access
defined('_JEXEC') or die('Restricted access'); 

class ApiViewKeys extends ApiView {
	
	public function display($tpl = null) {
		
		JHTML::stylesheet('com_api.css', 'components/com_api/assets/css/');
		
		if ($this->routeLayout($tpl)) :
			return;
		endif;
	
		$user	= JFactory::getUser();
	
		$model	= JModel::getInstance('Key', 'ApiModel');
		$model->setState('user_id', $user->get('id'));
		
		$tokens	= $model->listTokens();
		
		$new_token_link = JRoute::_('index.php?option=com_api&view=keys&layout=new');
		
		$this->assignRef('new_token_link', $new_token_link);
		$this->assignRef('user', $user);
		$this->assignRef('tokens', $tokens);
		
		parent::display($tpl);
	}	
	
	protected function displayNew($tpl=null) {
		
		JHTML::script('joomla.javascript.js', 'includes/js/');
		
		$this->assignRef('return', $_SERVER['HTTP_REFERER']);
		
		parent::display($tpl);
	}
		
}