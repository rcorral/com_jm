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

class UsersApiResourceUsers extends ApiResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_users/models/users.php';
		require_once JPATH_PLUGINS.'/api/users/resources/helper.php';

		$model = JModel::getInstance('ApiHelperModel', 'UsersModel');
		$model->_setCache('getstart', $model->getState('list.start'));
		$users = $model->getItems();

		if ( false === $users || ( empty( $users ) && $model->getError() ) ) {
			$response = $this->getErrorResponse( 400, $model->getError() );
		} else {
			$response = $users;
		}

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}