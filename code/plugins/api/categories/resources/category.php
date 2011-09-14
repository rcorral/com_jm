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

class CategoriesApiResourceCategory extends ApiResource
{
	public function get()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_categories/models/category.php';
		$model = JModel::getInstance( 'category', 'categoriesModel' );

		$this->plugin->setResponse( $model->getItem( JRequest::getInt( 'id', 0 ) )->getProperties() );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}