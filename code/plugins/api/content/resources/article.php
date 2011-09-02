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
		$response = new stdClass;
		$db = JFactory::getDBO();

		jimport( 'joomla.utilities.utility' );
		require_once JPATH_ADMINISTRATOR .DS. 'components' .DS. 'com_content' .DS. 'helper.php';

		// Set variables to be used
		APIHelper::setSessionUser();

		if ( ( $id = $this->saveContent() ) && false !== $id ) {
			$response->success = parent::get( 'message' );
			$response->id = $id;
		} else {
			$response->error = 'An error ocurred: ' . $this->getError();
		}

		$this->plugin->setResponse( $response );
	}

	/**
	 * Basically copied from com_content
	 * Needed it to return a few important things, and there is no hooks "yay joomla!"
	 * therefore, here it is...copied
	 */
	function saveContent()
	{
		global $mainframe;

		// Initialize variables
		$db		= & JFactory::getDBO();
		$user		= & JFactory::getUser();
		$dispatcher 	= & JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		$details	= JRequest::getVar( 'details', array(), 'post', 'array');
		$option		= JRequest::getCmd( 'option' );
		$task		= JRequest::getCmd( 'task' );
		$sectionid	= JRequest::getVar( 'sectionid', 0, '', 'int' );
		$redirect	= JRequest::getVar( 'redirect', $sectionid, 'post', 'int' );
		$menu		= JRequest::getVar( 'menu', 'mainmenu', 'post', 'menutype' );
		$menuid		= JRequest::getVar( 'menuid', 0, 'post', 'int' );
		$nullDate	= $db->getNullDate();

		$row = & JTable::getInstance('content');
		if (!$row->bind(JRequest::get('post'))) {
			$this->setError( $db->stderr() );
			return false;
		}
		$row->bind($details);

		// sanitise id field
		$row->id = (int) $row->id;

		$isNew = true;
		// Are we saving from an item edit?
		if ($row->id) {
			$isNew = false;
			$datenow =& JFactory::getDate();
			$row->modified 		= $datenow->toMySQL();
			$row->modified_by 	= $user->get('id');
		}

		$row->created_by 	= $row->created_by ? $row->created_by : $user->get('id');

		if ($row->created && strlen(trim( $row->created )) <= 10) {
			$row->created 	.= ' 00:00:00';
		}

		$config =& JFactory::getConfig();
		$tzoffset = $config->getValue('config.offset');
		$date =& JFactory::getDate($row->created, $tzoffset);
		$row->created = $date->toMySQL();

		// Append time if not added to publish date
		if (strlen(trim($row->publish_up)) <= 10) {
			$row->publish_up .= ' 00:00:00';
		}

		$date =& JFactory::getDate($row->publish_up, $tzoffset);
		$row->publish_up = $date->toMySQL();

		// Handle never unpublish date
		if (trim($row->publish_down) == JText::_('Never') || trim( $row->publish_down ) == '')
		{
			$row->publish_down = $nullDate;
		}
		else
		{
			if (strlen(trim( $row->publish_down )) <= 10) {
				$row->publish_down .= ' 00:00:00';
			}
			$date =& JFactory::getDate($row->publish_down, $tzoffset);
			$row->publish_down = $date->toMySQL();
		}

		// Get a state and parameter variables from the request
		$row->state	= JRequest::getVar( 'state', 0, '', 'int' );
		$params		= JRequest::getVar( 'params', null, 'post', 'array' );

		// Build parameter INI string
		if (is_array($params))
		{
			$txt = array ();
			foreach ($params as $k => $v) {
				$txt[] = "$k=$v";
			}
			$row->attribs = implode("\n", $txt);
		}

		// Get metadata string
		$metadata = JRequest::getVar( 'meta', null, 'post', 'array');
		if (is_array($metadata))
		{
			$txt = array();
			foreach ($metadata as $k => $v) {
				if ($k == 'description') {
					$row->metadesc = $v;
				} elseif ($k == 'keywords') {
					$row->metakey = $v;
				} else {
					$txt[] = "$k=$v";
				}
			}
			$row->metadata = implode("\n", $txt);
		}

		// Prepare the content for saving to the database
		ContentHelper::saveContentPrep( $row );

		// Make sure the data is valid
		if (!$row->check()) {
			$this->setError( $db->stderr() );
			return false;
		}

		// Increment the content version number
		$row->version++;

		$result = $dispatcher->trigger('onBeforeContentSave', array(&$row, $isNew));
		if(in_array(false, $result, true)) {
			$this->setError( $row->getError());
			return false;
		}

		// Store the content to the database
		if (!$row->store()) {
			$this->setError( $db->stderr() );
			return false;
		}

		// Check the article and update item order
		$row->checkin();
		$row->reorder('catid = '.(int) $row->catid.' AND state >= 0');

		/*
		 * We need to update frontpage status for the article.
		 *
		 * First we include the frontpage table and instantiate an instance of it.
		 */
		require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.'com_frontpage'.DS.'tables'.DS.'frontpage.php');
		$fp = new TableFrontPage($db);

		// Is the article viewable on the frontpage?
		if (JRequest::getVar( 'frontpage', 0, '', 'int' ))
		{
			// Is the item already viewable on the frontpage?
			if (!$fp->load($row->id))
			{
				// Insert the new entry
				$query = 'INSERT INTO #__content_frontpage' .
						' VALUES ( '. (int) $row->id .', 1 )';
				$db->setQuery($query);
				if (!$db->query())
				{
					$this->setError( $db->stderr() );
					return false;
				}
				$fp->ordering = 1;
			}
		}
		else
		{
			// Delete the item from frontpage if it exists
			if (!$fp->delete($row->id)) {
				$msg .= $fp->stderr();
			}
			$fp->ordering = 0;
		}
		$fp->reorder();

		$cache = & JFactory::getCache('com_content');
		$cache->clean();

		$dispatcher->trigger('onAfterContentSave', array(&$row, $isNew));

		$this->set( 'message', JText::sprintf( 'Successfully saved article', $row->title ) );

		return $row->id;
	}
}