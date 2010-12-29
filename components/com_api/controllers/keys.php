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
		
		$id		= JRequest::getInt('id', 0, 'post');
		$domain	= JRequest::getVar('domain', '', 'post', 'string');
		
		$data	= array(
			'id'		=> $id,
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
	
	public function delete() {
		JRequest::checkToken('request') or jexit(JText::_("COM_API_INVALID_TOKEN"));
		
		$user_id	= JFactory::getUser()->get('id');
		$id 		= JRequest::getInt('id', 0);
		
		$table 	= JTable::getInstance('Key', 'ApiTable');
		$table->load($id);
		
		if ($user_id != $table->user_id) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_API_UNAUTHORIZED_DELETE_KEY"), 'error');
			return false;
		endif;
		
		$table->delete($id);
		
		$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_API_SUCCESSFUL_DELETE_KEY"));
		
	}
	
}