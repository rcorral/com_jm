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

class ApiResourceArticles extends ApiResource {
	
	public function get() {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__content";
		
		if ($categoryid = JRequest::getInt('categoryid', 0))
		{
			$query .= " WHERE catid = ".$categoryid;
		}
		
		$db->setQuery($query);
		$articles = $db->loadObjectList();
		$this->plugin->setResponse($articles);
	}

	public function post() {
		$this->plugin->setResponse('here is a post request');
	}



}