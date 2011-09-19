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

class MenusApiResourceMenuItems extends ApiResource
{
	public function get()
	{
		ApiHelper::setSessionUser();

		require_once JPATH_ADMINISTRATOR.'/components/com_menus/models/items.php';

		$model = JModel::getInstance('Items', 'MenusModel');
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