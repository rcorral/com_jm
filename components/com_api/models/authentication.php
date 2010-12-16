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
	
	public function generateToken() {
		$user_id			= JFactory::getUser()->get('id');
		$table 				= JTable::getInstance('Token', 'ApiTable');
		$table->user_id		= 7097;
		$table->domain		= 'localhost';
		$table->created		= gmdate("Y-m-d H:i:s");
		$table->created_by	= $user_id;
		$table->hash		= $this->generateUniqueHash();
		$table->store();
	}
	
	private function generateUniqueHash() {
		$seed	= $this->makeRandomSeed();
		$hash	= sha1(uniqid($seed.microtime()));
		
		$this->_db->setQuery('SELECT COUNT(*) FROM #__api_tokens WHERE hash = "'.$hash.'"');
		$exists	= $this->_db->loadResult();
		
		if ($exists) :
			return $this->generateUniqueHash();
		else :
			return $hash;
		endif;
	}
	
	private function makeRandomSeed() {
		$string	= 'abcdefghijklmnopqrstuvwxyz';
		$alpha	= str_split($string.strtoupper($string));
		$last	= count($alpha)-1;
		
		$seed	= null;
		for ($i=0; $i<16; $i++) :
			$seed .= $alpha[mt_rand(0, $last)];
		endfor;
		return $seed;
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
				$this->setError(JText::_('COM_API_KEY_DOES_NOT_MATCH_DOMAIN'));
				return false;
			endif;
		endif;
		
		return $token->user_id;
	}
	
	public function loadTokenByHash($hash) {
		$this->_db->setQuery("SELECT * FROM #__api_tokens WHERE hash = '".$hash."'");
		$token	= $this->_db->loadObject();
		return $token;
	}
	
}