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

class MenusApiResourceMenus extends ApiResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_menus/models/menus.php';
		require_once JPATH_PLUGINS.'/api/menus/resources/helper.php';

		$model = JModel::getInstance('ApiHelperModel', 'MenusModel');
		$model->_setCache('getstart', $model->getState('list.start'));
		$menus = $model->getItems();

		if ( false === $menus || ( empty( $menus ) && $model->getError() ) ) {
			$response = $this->getErrorResponse( 400, $model->getError() );
		} else {
			$response = $menus;
		}

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}