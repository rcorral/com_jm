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
			$ip = JRequest::getVar('REMOTE_ADDR', '', 'server');
			if ($ip != $token->domain) :
				if (!APICache::callback($this, 'checkDomain', array($ip, $token->domain), APICache::HALF_DAY, true)) :
					$this->setError(JText::_('COM_API_KEY_DOES_NOT_MATCH_DOMAIN'));
					return false;
				endif;
			endif;
		endif;
		
		return $token->user_id;
	}
	
	public function checkDomain($ip, $domain) {
		// A simple IP check.  There must be a better way to do this.
		$expected_ip = gethostbyname($domain);		
		return $ip == $expected_ip;
	}
	
	public function loadTokenByHash($hash) {
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__api_keys WHERE hash = '".$hash."'");
		$token	= $db->loadObject();
		return $token;
	}
	
}