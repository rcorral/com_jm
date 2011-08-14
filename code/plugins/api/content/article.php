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

class ContentApiResourceArticle extends ApiResource {
	
	public function get() {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__content";
		
		if ($id = JRequest::getInt('id', 0))
		{
			$query .= " WHERE id = ".$id;
		}
		
		$db->setQuery($query);
		$article = $db->loadObjectList();
		$this->plugin->setResponse($article);
	}

	public function post() {
		$this->plugin->setResponse('here is a post request');
	}



}