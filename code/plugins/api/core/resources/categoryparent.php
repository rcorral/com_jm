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

class CoreApiResourceCategoryParent extends ApiResource
{
	/**
	 * A copy with modifications for the api from the categoryparent.php
	 * field on the categories component
	 */
	public function get()
	{
		$options = array();

		JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__categories AS a');
		$query->join('LEFT', '`#__categories` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		// Filter by the type
		if ($extension = JRequest::getVar('extension')) {
			$query->where('(a.extension = '.$db->quote($extension).' OR a.parent_id = 0)');
		}

		// Prevent parenting to children of this item.
		if ($id = JRequest::getInt('id')) {
			$query->join('LEFT', '`#__categories` AS p ON p.id = '.(int) $id);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');

			$rowQuery	= $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
			$rowQuery->from('#__categories AS a');
			$rowQuery->where('a.id = ' . (int) $id);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}

		$query->where('a.published IN (0,1)');
		$query->group('a.id');
		$query->order('a.lft ASC');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			// Translate ROOT
			if ($options[$i]->level == 0) {
				$options[$i]->text = JText::_('JGLOBAL_ROOT_PARENT');
			}

			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		// Initialise variables.
		$user = JFactory::getUser( APIHelper::getAPIUserId() );

		if (empty($id)) {
			// New item, only have to check core.create.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.create', $extension.'.category.'.$option->value)) {
					unset($options[$i]);
				}
			}
		} else {
			// Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.edit', $extension.'.category.'.$option->value)) {
					// As a backup, check core.edit.own
					if (!$user->authorise('core.edit.own', $extension.'.category.'.$option->value)) {
						// No core.edit nor core.edit.own - bounce this one
						unset($options[$i]);
					} else {
						// TODO I've got a funny feeling we need to check core.create here.
						// Maybe you can only get the list of categories you are allowed to create in?
						// Need to think about that. If so, this is the place to do the check.
					}
				}
			}
		}

		if (isset($row) && !isset($options[0])) {
			if ($row->parent_id == '1') {
				$parent = new stdClass();
				$parent->text = JText::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}
		}

		$this->plugin->setResponse( $options );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}