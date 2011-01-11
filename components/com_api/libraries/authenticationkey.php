<?php 
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiAuthenticationKey extends ApiAuthentication {
	
	protected	$auth_method		= null;
	protected	$domain_checking	= null;
	
	public function authenticate() {
		$key	= JRequest::getVar('key');
		$token	= $this->loadTokenByHash($key);
		
		if (!$token) :
			$this->setError(JText::_('COM_API_KEY_NOT_FOUND'));
			return false;
		endif;
		
		if (!$token->published) :
			$this->setError(JText::_('COM_API_KEY_DISABLED'));
			return false;
		endif;
		
		if ($this->get('domain_checking')) :
			$server_name = JRequest::getVar('SERVER_NAME', '', 'server');
			if ($server_name != $token->domain) :
				$pattern = '/\.'.$token->domain.'$/i';
				if (!preg_match($pattern, $server_name)) :
					$this->setError(JText::_('COM_API_KEY_DOES_NOT_MATCH_DOMAIN'));
					return false;
				endif;
			endif;
		endif;
		
		return $token->user_id;
	}
	
	public function loadTokenByHash($hash) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__api_keys WHERE hash = '".$hash."'");
		$token	= $db->loadObject();
		return $token;
	}
	
}