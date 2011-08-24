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

class ContentApiResourceArticles extends ApiResource
{
	public function get()
	{
		$db = JFactory::getDBO();
		$where = ' WHERE `state` != -2';

		if ( $categoryid = JRequest::getInt( 'categoryid', 0 ) ) {
			$query .= " AND `catid` = {$categoryid}";
		}
		
		$query = "SELECT * FROM #__content";

		$db->setQuery( $query );
		$articles = $db->loadObjectList();

		$this->plugin->setResponse( $articles );
	}

	public function post()
	{
		$response = new stdClass;

		switch ( JRequest::getVar( 'task' ) ) {
			case 'delete':
				if ( $this->deleteArticle() ) {
					$response->success = parent::get( 'message' );
				} else {
					$response->error = $this->getError();
				}

				break;

			default:
				$response->message = 'Nothing to do...exiting';
				break;
		}

		$this->plugin->setResponse( $response );
	}

	public function deleteArticle( $id = null )
	{
		global $mainframe;

		if ( !$id ) {
			$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		}

		// Initialize variables
		$db			= & JFactory::getDBO();

		$option		= JRequest::getCmd( 'option' );
		$return		= JRequest::getCmd( 'returntask', '', 'post' );
		$nullDate	= $db->getNullDate();

		JArrayHelper::toInteger( $cid );

		if ( count( $cid ) < 1 ) {
			$this->setError( JText::_( 'Select an item to delete' ) );
		}

		// Removed content gets put in the trash [state = -2] and ordering is always set to 0
		$state		= '-2';
		$ordering	= '0';

		// Get the list of content id numbers to send to trash.
		$cids = implode(',', $cid);

		// Update articles in the database
		$query = 'UPDATE #__content' .
				' SET state = '.(int) $state .
				', ordering = '.(int) $ordering .
				', checked_out = 0, checked_out_time = '.$db->Quote($nullDate).
				' WHERE id IN ( '. $cids. ' )';
		$db->setQuery($query);
		if (!$db->query())
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		$this->set( 'message', JText::_( 'Item sent to the trash' ) );
		return true;
	}
}