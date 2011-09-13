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

class CategoriesApiResourceCategory extends ApiResource
{
	public function get()
	{
		$extension  = JRequest::getWord( 'extension' );

		require_once JPATH_ADMINISTRATOR.'/components/com_categories/models/categories.php';
		require_once JPATH_PLUGINS.'/api/categories/resources/helper.php';

		$model = JModel::getInstance('ApiHelperModel', 'CategoriesModel');
		$model->_setCache('getstart', $model->getState('list.start'));
		$categories = $model->getItems();

		if ( false === $categories ) {
			$response = $this->getErrorResponse( 400, $model->getError() );
		} else {
			$response = $categories;
		}

		$this->plugin->setResponse( $categories );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}