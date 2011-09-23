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

class CoreApiResourceMenuParent extends ApiResource
{
	public function get()
	{
		// Initialize variables.
		JFactory::getLanguage()->load( 'com_menus', JPATH_ADMINISTRATOR );
		$options = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('a.id AS value, a.title AS text, a.level');
		$query->from('#__menu AS a');
		$query->join('LEFT', '`#__menu` AS b ON a.lft > b.lft AND a.rgt < b.rgt');

		if ($menuType = JRequest::getVar('menutype')) {
			$query->where('a.menutype = '.$db->quote($menuType));
		}
		else {
			$query->where('a.menutype != '.$db->quote(''));
		}

		// Prevent parenting to children of this item.
		if ($id = JRequest::getInt( 'id' )) {
			$query->join('LEFT', '`#__menu` AS p ON p.id = '.(int) $id);
			$query->where('NOT(a.lft >= p.lft AND a.rgt <= p.rgt)');
		}

		$query->where('a.published != -2');
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
		for ($i = 0, $n = count($options); $i < $n; $i++) {
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}

		array_unshift( $options, JHtml::_('select.option', 1, JText::_('COM_MENUS_ITEM_ROOT')) );

		$this->plugin->setResponse( $options );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}