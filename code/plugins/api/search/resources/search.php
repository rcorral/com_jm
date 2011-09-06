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
require_once(JPATH_SITE.DS.'components'.DS.'com_search'.DS.'models'.DS.'search.php');
require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_search'.DS.'helpers'.DS.'search.php');

class SearchApiResourceSearch extends ApiResource
{
	public function get()
	{

		$searchmodel = new SearchModelSearch();//JFactory::getModel('search');
		$results = $searchmodel->getData();

		foreach ($results as $k=>$v) {
			$results[$k]->href = JURI::root() . $v->href;
		}
		$this->plugin->setResponse( $results );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}
