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
		require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/category.php';
		$model = JModel::getInstance( 'category', 'categoriesModel' );

		$this->plugin->setResponse( $model->getItem( JRequest::getInt( 'id', 0 ) )->getProperties() );
	}

	public function post()
	{
		// Set variables to be used
		APIHelper::setSessionUser();

		// Include dependencies
		jimport('joomla.database.table');
		$language = JFactory::getLanguage();
		$language->load('joomla', JPATH_ADMINISTRATOR);
		$language->load('com_categories', JPATH_ADMINISTRATOR);

		require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/category.php';

		// Fake parameters
		$_POST['task'] = 'apply';
		$_REQUEST['task'] = 'apply';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$context = 'category';

		// Clear userstate just in case
		$model = JModel::getInstance( 'Category', 'CategoriesModel' );
		$success = $model->save( $data );

		if ( $model->getError() ) {
			$response = $this->getErrorResponse( 400, $model->getError() );
		} elseif ( !$success ) {
			$response = $this->getErrorResponse( 400, JText::_('COM_API_ERROR_OCURRED') );
		} else {
			$response = $this->getSuccessResponse( 201, JText::_('COM_CATEGORIES_SAVE_SUCCESS') );
			// Get the ID of the category that was modified or inserted
			$response->id = $model->get('state')->get($context.'.id');

			// Checkin category
			$model->checkin( $response->id );
		}

		$this->plugin->setResponse( $response );
	}

	public function put()
	{	
		$data = JRequest::getVar('jform', array(), 'post', 'array');

		// Fake parameters
		if ( !JRequest::getInt( 'id' ) ) {
			$_POST['id'] = $data['id'];
			$_REQUEST['id'] = $data['id'];
		}

		// Simply call post as Joomla will just save a category with an id
		$this->post();

		$response = $this->plugin->get( 'response' );
		if ( isset( $response->success ) && $response->success ) {
			JResponse::setHeader( 'status', 200, true );
			$response->code = 200;
			$this->plugin->setResponse( $response );
		}
	}
}