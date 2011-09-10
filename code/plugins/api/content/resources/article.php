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

class ContentApiResourceArticle extends ApiResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
		$model = JModel::getInstance( 'article', 'contentModel' );

		$this->plugin->setResponse( $model->getItem( JRequest::getInt( 'id', 0 ) )->getProperties() );
	}

	public function post()
	{
		// Set variables to be used
		APIHelper::setSessionUser();

		// Include dependencies
		jimport('joomla.application.component.controller');

		require_once JPATH_ADMINISTRATOR . '/components/com_content/controllers/article.php';
		require_once JPATH_ADMINISTRATOR . '/components/com_content/models/article.php';
		JForm::addFormPath( JPATH_ADMINISTRATOR . '/components/com_content/models/forms/' );
		JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_content/tables/' );

		// Fake parameters
		$_POST['task'] = 'apply';
		$_REQUEST['task'] = 'apply';
		$_REQUEST[JUtility::getToken()] = 1;
		$_POST[JUtility::getToken()] = 1;

		$context = 'com_content.edit.article';
		$app = JFactory::getApplication();
		// Clear userstate just in case
		$app->setUserState($context.'.id', array());
		$controller = new ContentControllerArticle();
		$success = $controller->execute('apply');

		if ( $controller->getError() ) {
			$response = $this->getErrorResponse( 400, $controller->getError() );
		} elseif ( !$success ) {
			$response = $this->getErrorResponse( 400, JText::_('COM_API_ERROR_OCURRED') );
		} else {
			$response = $this->getSuccessResponse( 201, JText::_('JLIB_APPLICATION_SAVE_SUCCESS') );
			// Kind of a weird way of doing this, there has to be a better way?
			$values	= (array) $app->getUserState($context.'.id');
			$response->id = array_pop( $values );
			$app->setUserState($context.'.id', $values);
			// Checkin article
			$controller->getModel()->checkin( $response->id );
		}

		$this->plugin->setResponse( $response );
	}

	public function put()
	{	
		$app = JFactory::getApplication();
		$data = JRequest::getVar('jform', array(), 'post', 'array');
		$context = 'com_content.edit.article';

		// Fake parameters
		$values	= (array) $app->getUserState($context.'.id');
		array_push($values, (int) $data['id']);
		$values = array_unique($values);
		$app->setUserState($context.'.id', $values);
		if ( !JRequest::getInt( 'id' ) ) {
			$_POST['id'] = $data['id'];
			$_REQUEST['id'] = $data['id'];
		}

		// Simply call post as Joomla will just save an article with an id
		$this->post();

		$response = $this->plugin->get( 'response' );
		if ( $response->success ) {
			$this->plugin->setResponse( $this->getSuccessResponse( 200, $response->message ) );
		}
	}
}