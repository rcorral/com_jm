<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

/**
 * Class to get the API's version
 */
class ApiApiResourceToken extends ApiResource
{
	function __construct( ApiPlugin $plugin )
	{
		parent::__construct( $plugin );

		$auth_handler = APIAuthentication::getInstance('user');
		$user_id = $auth_handler->authenticate();
		if ( false === $user_id ) {
			throw new Exception( $auth_handler->getError(), 403 );
		}

		$this->plugin->setResourceAccess( array( 'token' ), 'public' );
		// $this->plugin->set('skip_request_limit', true);
		$this->plugin->set('user', $user_id);
	}

	public function get()
	{
		jimport( 'joomla.application.helper' );

		// Set variables to be used
		$db = JFactory::getDBO();
		$return_status = 200;
		$user_id = $this->plugin->get('user');
		APIHelper::setSessionUser($user_id);
		$error_code = 404;
		$error_response = JText::_('COM_API_KEY_NOT_FOUND');

		// Load users token
		$db->setQuery( "SELECT * FROM #__api_keys WHERE user_id = " . (int) $user_id );
		$token = $db->loadObject();

		// Check to see if we are forcing a token no matter what
		if ( JRequest::getInt('force') && ( !$token || !$token->published ) ) {
			$canDo = APIHelper::getActions();
			// Make sure that this user is allowed to create new keys
			if ( $canDo->get('core.manage') ) {
				// Create new token
				$data = array(
					'id'        => 0,
					'domain'    => '',
					'user_id'   => $user_id,
					'published' => 1
				);

				$model = JModel::getInstance('Key', 'ApiModel');
				$token = $model->save($data);
				if ( false === $token ) {
					$error_code = 500;
					$error_response = $model->getError();
				} else {
					$return_status = 201;
				}
			} else {
				$error_code = 401;
				$error_response = JText::_('COM_API_KEY_CREATE_UNAUTORIZED');
			}
		}

		if ( !$token ) {
			$response = $this->getErrorResponse( $error_code, $error_response );
		} elseif ( !$token->published ) {
			$response = $this->getErrorResponse( 404, JText::_('COM_API_KEY_DISABLED') );
		} else {
			$response = $this->getSuccessResponse( $return_status, JText::_('COM_API_SUCCESS') );
			$response->token = $token->hash;
		}

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}