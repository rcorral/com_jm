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

class ApiResourceArticle extends ApiResource {
	
	public function get() {
		$this->plugin->setResponse('here is a get request');
	}

	public function post() {
		$this->plugin->setResponse('here is a post request');
	}

	public function put() {
		$this->plugin->setResponse('here is a put request');
	}

	public function delete() {
		$this->plugin->setResponse('here is a delete request');
	}

}