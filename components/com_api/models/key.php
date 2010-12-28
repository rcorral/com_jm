<?php 
defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiModelKey extends ApiModel {
	
	public function __construct($config=array()) {
		parent::__construct($config);
	}
	
	public function listTokens() {
		$where = null;
		if($user_id	= $this->getState('user_id')) :
			$where = 'WHERE user_id = '.$this->_db->Quote($user_id);
		endif;
		
		$query = "SELECT hash, domain, enabled, created "
				."FROM #__api_tokens "
				.$where
				;
		$this->_db->setQuery($query);
		$tokens	= $this->_db->loadObjectList();
		return $tokens;
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
	
}