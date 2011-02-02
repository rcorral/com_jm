<?php

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