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

class CategoriesApiResourceCategories extends ApiResource
{
	public function get()
	{
		$extension  = JRequest::getWord( 'extension' );
		ApiHelper::setSessionUser();

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

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}

	public function delete( $id = null )
	{
		// Include dependencies
		jimport('joomla.application.component.controller');
		jimport('joomla.form.form');
		jimport('joomla.database.table');

		require_once JPATH_ADMINISTRATOR . '/components/com_categories/controllers/categories.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/category.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_categories/models/forms/' );

		// Fake parameters
		$_POST['task'] = 'trash';
		$_REQUEST['task'] = 'trash';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		JFactory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);
		$controller = new CategoriesControllerCategories();
		try {
			$controller->execute('trash');
		} catch ( JException $e ) {
			$success = false;
			$controller->set('messageType', 'error');
			$controller->set('message', $e->getMessage() );
		}

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( 'error' == $controller->get('messageType') ) {
			$response = $this->getErrorResponse( 400, $controller->get('message') );
		} else {
			$response = $this->getSuccessResponse( 200, $controller->get('message') );
		}

		$this->plugin->setResponse( $response );
	}
}