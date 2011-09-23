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

class MenusApiResourceMenuItem extends ApiResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';
		$model = JModel::getInstance( 'item', 'MenusModel' );

		$this->plugin->setResponse( $model->getItem( JRequest::getInt( 'id', 0 ) )->getProperties() );
	}

	public function post()
	{
		// Set variables to be used
		APIHelper::setSessionUser();
		$language = JFactory::getLanguage();
		$language->load('joomla', JPATH_ADMINISTRATOR);
		$language->load('com_menus', JPATH_ADMINISTRATOR);

		// Include dependencies
		jimport('joomla.application.component.controller');
		jimport('joomla.form.form');
		jimport('joomla.database.table');

		require_once JPATH_ADMINISTRATOR . '/components/com_menus/controllers/item.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_menus/models/item.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_menus/models/forms/' );

		// Fake parameters
		$_POST['task'] = 'apply';
		$_REQUEST['task'] = 'apply';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		$app = JFactory::getApplication();
		$context = 'com_menus.edit.item';

		// Save menuitem
		$controller = new MenusControllerItem();
		$success = $controller->execute('apply');

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( false === $success ) {
			$response = $this->getErrorResponse( 400, JText::_('COM_API_ERROR_OCURRED') );
		} else {
			$response = $this->getSuccessResponse( 201, $controller->get('message') );
			// Kind of a weird way of doing this, there has to be a better way?
			$values	= (array) $app->getUserState($context.'.id');
			$response->id = array_pop( $values );
			$app->setUserState($context.'.id', $values);
			// Checkin menuitem
			$controller->getModel()->checkin( $response->id );
		}

		// Clear userstate for future requests
		$app->setUserState($context.'.id', array());

		$this->plugin->setResponse( $response );
	}

	public function put()
	{	
		$app = JFactory::getApplication();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$context = 'com_menus.edit.item';

		// Fake parameters
		$values	= (array) $app->getUserState($context.'.id');
		array_push($values, (int) $data['id']);
		$values = array_unique($values);
		$app->setUserState($context.'.id', $values);
		if ( !JRequest::getInt( 'id' ) ) {
			$_POST['id'] = $data['id'];
			$_REQUEST['id'] = $data['id'];
		}

		// Simply call post as Joomla will just save an menuitem with an id
		$this->post();

		$response = $this->plugin->get( 'response' );
		if ( isset( $response->success ) && $response->success ) {
			JResponse::setHeader( 'status', 200, true );
			$response->code = 200;
			$this->plugin->setResponse( $response );
		}
	}
}