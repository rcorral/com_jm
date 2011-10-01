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

/**
 * Class to get the API's version
 */
class ApiApiResourceVersion extends ApiResource
{
	public function get()
	{
		jimport( 'joomla.application.helper' );

		$xml = JApplicationHelper::parseXMLInstallFile( JPATH_COMPONENT_ADMINISTRATOR.'/api.xml' );

		if ( false == $xml ) {
			$response = $this->getErrorResponse( 400, 'COM_API_UNEXPECTED_ERROR' );
		} else {
			$response = $this->getSuccessResponse( 200, JText::_('COM_API_SUCCESS') );
			$response->version = $xml['version'];

			// Add version that it is also backward compatible with
			$response->compatible_from = '0.1';
			$response->compatbile_msg = '';
		}

		$this->plugin->setResponse( $response );
	}

	public function post()
	{
		$this->plugin->setResponse( 'here is a post request' );
	}
}