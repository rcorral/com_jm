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

class ContentApiResourceArticleslist extends ApiResource
{
	public function get()
	{
		$db = JFactory::getDBO();

		// Get the pagination request variables
		$limit      = JRequest::getInt( 'limit', 20 );
		$limitstart = JRequest::getInt( 'limitstart', 0 );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? ( floor( $limitstart / $limit ) * $limit ) : 0 );

		$query = "SELECT c.`id`, c.`title`, c.`state`, c.`access`, c.`publish_up`,
		 	u.`name`
				FROM #__content AS c
				LEFT JOIN #__users AS u ON c.`created_by` = u.id
					WHERE c.`state` != -2";

		if ( $categoryid = JRequest::getInt( 'categoryid', 0 ) ) {
			$query .= " AND c.`catid` = {$categoryid}";
		}

		$query .= " ORDER BY c.`title` LIMIT {$limitstart}, {$limit}";

		$db->setQuery( $query );
		$articles = $db->loadObjectList();

		foreach ( $articles as &$row ) {
			$row->state = ( $row->state ) ? JText::_( 'COM_API_PUBLISHED' )
				: JText::_( 'COM_API_UNPUBLISHED' );

			switch ( $row->access ) {
				case 1:
					$row->access = JText::_( 'COM_API_ACCESS_REGISTERED' );
					break;

				case 2:
					$row->access = JText::_( 'COM_API_ACCESS_SPECIAL' );
					break;

				default:
					$row->access = JText::_( 'COM_API_ACCESS_PUBLIC' );
					break;
			}

			$row->date = date( 'Y-m-d', strtotime( $row->publish_up ) );
		}

		$this->plugin->setResponse( $articles );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}