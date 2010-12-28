<?php

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class ApiControllerKey extends ApiController {
	
	public function display() {
		
		if (!JFactory::getUser()->get('id')) :
			$uri = JFactory::getURI()->toString();
			$redirect = JRoute::_('index.php?option=com_user&view=login&return='.base64_encode($uri));
			JFactory::getApplication()->redirect($redirect, JText::_('COM_API_LOGIN_MSG'));
		endif;
		
		parent::display(false);
	}
	
}