<?php 
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die;

jimport( 'joomla.application.component.model' );

abstract class ApiAuthentication extends JObject
{
	protected $auth_method     = null;
	protected $domain_checking = null;
	static    $auth_errors     = array();

	public function __construct( $params )
	{
    	parent::__construct( $config );

		$this->set( 'auth_method', $params->get( 'auth_method', 'key' ) );
		$this->set( 'domain_checking', $params->get( 'domain_checking', 1 ) );
  	}

	abstract public function authenticate();

	public static function authenticateRequest()
	{
		$user_id		= APIHelper::getAPIUserID();

		if ( $user_id === false ) {
			self::setAuthError( $auth_handler->getError() );
			return false;
		} else {
			$user = JFactory::getUser($user_id);
			if ( !$user->id ) {
				self::setAuthError( JText::_( 'COM_API_USER_NOT_FOUND' ) );
				return false;
			}

			if ( $user->block == 1 ) {
				self::setAuthError( JText::_( 'COM_API_BLOCKED_USER' ) );
				return false;
			}

			return $user;	
		}
	}
	
	public static function setAuthError( $msg )
	{
		self::$auth_errors[] = $msg;
		return true;
	}

	public static function getAuthError()
	{
		if ( empty( self::$auth_errors ) ) {
			return false;
		}

		return array_pop( self::$auth_errors );
	}
	
}