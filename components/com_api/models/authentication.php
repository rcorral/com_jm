<?php 
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiModelAuthentication extends ApiModel {
	
	protected	$auth_method		= null;
	protected	$domain_checking	= null;
	
	public function __construct($config=array()) {
    	parent::__construct($config);
		$this->set('auth_method', 'key');
		$this->set('domain_checking', true);
  	}
	
	public function authenticateRequest() {
		if ($this->get('auth_method') == 'key') :
			$user_id = $this->authenticateKey();
		else :
			return false;
		endif;
		
		if ($user_id === false) :
			return false;
		else :
			$user	= JFactory::getUser($user_id);
			if (!$user->id) :
				$this->setError(JText::_("COM_API_USER_NOT_FOUND"));
				return false;
			endif;
			
			if ($user->block == 1) :
				$this->setError(JText::_("COM_API_BLOCKED_USER"));
				return false;
			endif;
			
			return $user;
			
		endif;
		
	}
	
	private function authenticateKey() {
		$key	= JRequest::getVar('key');
		$token	= $this->loadTokenByHash($key);
		
		if (!$token) :
			$this->setError(JText::_('COM_API_KEY_NOT_FOUND'));
			return false;
		endif;
		
		if (!$token->enabled) :
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
		$this->_db->setQuery("SELECT * FROM #__api_keys WHERE hash = '".$hash."'");
		$token	= $this->_db->loadObject();
		return $token;
	}
	
}