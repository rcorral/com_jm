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

class ContentApiResourceSections extends ApiResource
{
	public function get()
	{
		$db = JFactory::getDBO();
		$db->setQuery( 'SELECT * FROM #__sections' );
		$sections = $db->loadObjectList( 'id' );

		$this->plugin->setResponse( $sections );
	}

	public function post()
	{
		$post = JRequest::get('post');

		$alias = JFilterOutput::stringURLSafe($post['title']);
		$table = JTable::getInstance('Section', 'JTable');
		$table->title = $post['title'];
		$table->alias = $alias;
		$table->scope = 'content';
		$table->description = $post['introtext'];
		$table->published = 1;
		$table->store();

		$this->plugin->setResponse($table);
	}
}