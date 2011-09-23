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

class CoreApiResourceTemplateStyle extends ApiResource
{
	public function get()
	{
		// Initialize variables.
		$groups = array();
		$lang = JFactory::getLanguage();

		// Get the client and client_id.
		$clientName = JRequest::getWord('client', 'site');
		$client = JApplicationHelper::getClientInfo(($clientName) ? $clientName : 'site', true);

		// Get the template.
		$template = (string) JRequest::getWord('template', '');

		// Get the database object and a new query object.
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);

		// Build the query.
		$query->select('s.id, s.title, e.name as name, s.template');
		$query->from('#__template_styles as s');
		$query->where('s.client_id = '.(int) $client->id);
		$query->order('template');
		$query->order('title');
		if ($template) {
			$query->where('s.template = '.$db->quote($template));
		}
		$query->join('LEFT', '#__extensions as e on e.element=s.template');
		$query->where('e.enabled=1');

		// Set the query and load the styles.
		$db->setQuery($query);
		$styles = $db->loadObjectList();

		// Build the grouped list array.
		if ($styles)
		{
			foreach($styles as $style) {
				$template = $style->template;
				$lang->load('tpl_'.$template.'.sys', $client->path, null, false, false)
			||	$lang->load('tpl_'.$template.'.sys', $client->path.'/templates/'.$template, null, false, false)
			||	$lang->load('tpl_'.$template.'.sys', $client->path, $lang->getDefault(), false, false)
			||	$lang->load('tpl_'.$template.'.sys', $client->path.'/templates/'.$template, $lang->getDefault(), false,false);
				$name = JText::_($style->name);
				// Initialize the group if necessary.
				if (!isset($groups[$name])) {
					$groups[$name] = array();
				}

				$groups[$name][] = JHtml::_('select.option', $style->id, $style->title);
			}
		}

		array_unshift( $groups, JHtml::_('select.option', 0, JText::_('JOPTION_USE_DEFAULT')) );

		$this->plugin->setResponse( $groups );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}