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
		$db = JFactory::getDBO();

		jimport( 'joomla.html.parameter' );

		$extras = explode( ',', JRequest::getVar( 'extras' ) );
		$all_extras = in_array( 'all', $extras );

		$select = 'c.*';
		$join = '';
		$where = '';

		if ( $all_extras || in_array( 'frontpage', $extras ) ) {
			$select .= ', f.`content_id` AS is_frontpage';
			$join .= ' LEFT JOIN #__content_frontpage AS f ON c.`id` = f.`content_id`';
		}

		$query = "SELECT {$select}
			FROM #__content AS c
			{$join}
				WHERE c.`id` = " . JRequest::getInt( 'id', 0 );

		$db->setQuery( $query );
		$article = $db->loadObject();

		if ( $all_extras || in_array( 'parseparams', $extras ) ) {
			$_meta    = new JParameter( $article->metadata );
			$_attribs = new JParameter( $article->attribs );
			$article->attribs = $_attribs->_registry['_default']['data'];
			$article->metadata = $_meta->_registry['_default']['data'];
		}

		$this->plugin->setResponse( $article );
	}

	public function post()
	{
		$response = new stdClass;

		jimport( 'joomla.utilities.utility' );
		require_once JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_content' .DS. 'controller.php';

		// Set variables to be used
		APIHelper::setSessionUser();
		JRequest::setVar( JUtility::getToken(), '1' );
			// This needs to be here to avoid the redirects
		APIHelper::loadFakeMainframe();

		require_once JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_content' .DS. 'helper.php';

		if ( false === ContentController::saveContent() ) {
			$response->error = JError::getErrors();
		} else {
			$response->success = JText::_( 'Successfully saved changes to article' );
		}

		APIHelper::restoreMainframe();

		$this->plugin->setResponse( $response );
	}
}