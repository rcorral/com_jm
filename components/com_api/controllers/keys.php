<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerKeys extends ApiController {
	
	public function display() {
		
		if (!$this->checkAccess()) :
			$user_id = JFactory::getUser()->get('id');
			
			if (!$user_id) :
				$uri = JFactory::getURI()->toString();
				$redirect = JRoute::_('index.php?option=com_user&view=login&return='.base64_encode($uri));
				$msg = JText::_('COM_API_LOGIN_MSG');
			else :
				$redirect = 'index.php';
				$msg = JText::_('COM_API_NOT_AUTH_MSG');
			endif;
			JFactory::getApplication()->redirect($redirect, $msg);
			return;
		endif;
		
		parent::display(false);
	}

	private function checkAccess() {
		$user	= JFactory::getUser();

		if ($user->get('gid') == 25) :
			return true;
		endif;
		
		$params	= JComponentHelper::getParams('com_api');
		
		if (!$params->get('key_registration')) :
			return false;
		endif;
		
		$access_level = $params->get('key_registration_access');
		
		if ($user->get('gid') < $access_level) :
			return false;
		endif;
		
		return true;
	}

	public function cancel() {
		JRequest::checkToken() or jexit(JText::_("COM_API_INVALID_TOKEN"));
		$this->setRedirect(JRoute::_('index.php?option=com_api&view=keys'));
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