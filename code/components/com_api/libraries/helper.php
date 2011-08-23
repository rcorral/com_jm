<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Rafael Corral
 * @link 	http://www.corephp.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class APIHelper
{
	function getAPIUserID()
	{
		$params			= JComponentHelper::getParams( 'com_api' );
		$method			= $params->get( 'auth_method', 'key' );
		$className 		= 'APIAuthentication' . ucwords( $method );
		$auth_handler 	= new $className( $params );

		return $auth_handler->authenticate();
	}

	function setSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->set( 'user', JUser::getInstance( APIHelper::getAPIUserID() ) );
	}

	function unsetSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->clear( 'user' );
	}

	function loadFakeMainframe()
	{
		global $mainframe, $mainframe_backup;
		static $has_loaded = false;

		if ( !$has_loaded ) {
			require JPATH_COMPONENT .DS. 'libraries' .DS. 'helpers' .DS. 'fakeapplication.php';

			$has_loaded = true;
		}

		$mainframe_backup = $mainframe;
		$mainframe = JApplicationFake::getInstance();
	}

	function restoreMainframe()
	{
		global $mainframe, $mainframe_backup;
		$mainframe = $mainframe_backup;
	}
}