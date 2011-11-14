<?php
/**
 * @package	JM
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class JMControllerKeys extends JMController {
	
	public function display() {
		parent::display();
	}

	private function checkAccess() {
		$user	= JFactory::getUser();

		if ($user->get('gid') == 25) :
			return true;
		endif;
		
		$params	= JComponentHelper::getParams('com_jm');
		
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
		JRequest::checkToken() or jexit(JText::_("COM_JM_INVALID_TOKEN"));
		$this->setRedirect(JRoute::_('index.php?option=com_jm&view=keys', FALSE));
	}
	
	public function save() {
		JRequest::checkToken() or jexit(JText::_("COM_JM_INVALID_TOKEN"));
		
		$id		= JRequest::getInt('id', 0, 'post');
		if (!$id && !$this->checkAccess()) :
			JFactory::getApplication()->redirect('index.php', JText::_('COM_JM_NOT_AUTH_MSG'));
			exit();
		endif;
		
		$domain	= JRequest::getVar('domain', '', 'post', 'string');
		
		$data	= array(
			'id'		=> $id,
			'domain'	=> $domain,
			'user_id'	=> JFactory::getUser()->get('id'),
			'enabled'	=> 1
		);
		
		$model	= JModel::getInstance('Key', 'JMModel');
		
		if ($model->save($data) === false) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], $model->getError(), 'error');
			return false;
		endif;
		
		$this->setRedirect(JRoute::_('index.php?option=com_jm&view=keys'), JText::_('COM_JM_KEY_SAVED'));
		
	}
	
	public function delete() {
		JRequest::checkToken('request') or jexit(JText::_("COM_JM_INVALID_TOKEN"));
		
		if (!$this->checkAccess()) :
			JFactory::getApplication()->redirect('index.php', JText::_('COM_JM_NOT_AUTH_MSG'));
			exit();
		endif;
		
		$user_id	= JFactory::getUser()->get('id');
		$id 		= JRequest::getInt('id', 0);
		
		$table 	= JTable::getInstance('Key', 'JMTable');
		$table->load($id);
		
		if ($user_id != $table->user_id) :
			$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_JM_UNAUTHORIZED_DELETE_KEY"), 'error');
			return false;
		endif;
		
		$table->delete($id);
		
		$this->setRedirect($_SERVER['HTTP_REFERER'], JText::_("COM_JM_SUCCESSFUL_DELETE_KEY"));
		
	}
	
}