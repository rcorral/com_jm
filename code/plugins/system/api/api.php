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

class plgSystemApi extends JPlugin
{	
	function __construct( &$subject, $config )
	{
		parent::__construct( $subject, $config );
	}

	function onAfterRoute()
	{
		$app = JFactory::getApplication();

		if ( 'com_api' == JRequest::getVar( 'option' ) && $app->isSite() ) {
			JRequest::setVar( 'format', 'raw' );
		}
	}
}