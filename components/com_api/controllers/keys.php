<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerKeys extends ApiController {
	
	public function display() {
		
		if (!JFactory::getUser()->get('id')) :
			$uri = JFactory::getURI()->toString();
			$redirect = JRoute::_('index.php?option=com_user&view=login&return='.base64_encode($uri));
			JFactory::getApplication()->redirect($redirect, JText::_('COM_API_LOGIN_MSG'));
		endif;
		
		parent::display(false);
	}

	public function cancel() {
		JRequest::checkToken() or jexit(JText::_("COM_API_INVALID_TOKEN"));
		$return = JRequest::getVar('return', 'index.php', 'post');
		$this->setRedirect($return);
	}
	
	public function save() {
		JRequest::checkToken() or jexit(JText::_("COM_API_INVALID_TOKEN"));
		
		$domain	= JRequest::getVar('domain', '', 'post', 'string');
		
		$data	= array(
			'domain'	=> $domain,
			'user_id'	=> JFactory::getUser()->get('id'),
			'enabled'	=> 1
		);
		
		$model	= JModel::getInstance('Key', 'ApiModel');
		
		if ($model->save($data) === false) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], $model->getError(), 'error');
			return false;
		endif;
		
		$this->setRedirect(JRoute::_('index.php?option=com_api&view=keys'), JText::_('COM_API_KEY_SAVED'));
		
	}
	
}