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

class LanguageApiResourceLanguages extends ApiResource
{
	public function get()
	{
		jimport( 'joomla.language.helper' );

		$client = JRequest::getCmd( 'client', 'site' );

		$languages = JLanguageHelper::createLanguageList(
			'', constant( 'JPATH_' . strtoupper( $client ) ), true );

		$this->plugin->setResponse( $languages );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}