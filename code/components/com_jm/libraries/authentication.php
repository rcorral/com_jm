<?php 
/**
 * @package	JM
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

abstract class JMAuthentication extends JObject
{
	protected $auth_method     = null;
	protected $domain_checking = null;

	public function __construct( $params )
	{
    	parent::__construct( $params );
  	}

	public function getInstance( $method = null )
	{
		static $instances = array();
		
		jimport('joomla.application.component.helper');
		$params = JComponentHelper::getParams( 'com_jm' );

		if ( null == $method ) {
			$method = $params->get( 'auth_method', 'key' );
		}

		if ( isset( $instances[$method] ) ) {
			return $instances[$method];
		}

		$className    = 'JMAuthentication' . ucwords( $method );
		$auth_handler = new $className( $params->toArray() );
		$instances[$method] = $auth_handler;

		return $instances[$method];
	}

	abstract public function authenticate();

	public function authenticateRequest()
	{
		$user_id = JMHelper::getJMUserID();

		if ( $user_id === false ) {
			return false;
		} else {
			$user = JFactory::getUser( $user_id );
			if ( !$user->id ) {
				$this->setError( JText::_( 'COM_JM_USER_NOT_FOUND' ) );
				return false;
			}

			if ( $user->block == 1 ) {
				$this->setError( JText::_( 'COM_JM_BLOCKED_USER' ) );
				return false;
			}

			return $user;	
		}
	}
}