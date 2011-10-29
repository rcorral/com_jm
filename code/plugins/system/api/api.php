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

	public static function getPutParameters( $input )
	{
		$putdata = $input;
		if ( function_exists('mb_parse_str') ) {
	    	mb_parse_str( $putdata, $outputdata );
		} else {
			parse_str( $putdata, $outputdata );
		}

    	return $outputdata;
	}
}

$app = JFactory::getApplication();

// Temporary hack until it is possible to get Joomla to add PUT, DELETE support
if (  $app->isSite() && in_array( JRequest::getMethod(), array( 'PUT', 'DELETE' ) ) ) {
	$putdata = plgSystemApi::getPutParameters(file_get_contents('php://input'));
	$putdata['format'] = 'raw';

	if ( isset( $putdata['option'] ) && 'com_api' == $putdata['option'] ) {
		$_REQUEST = array_merge( $_REQUEST, $putdata );
		$_POST = array_merge( $_POST, $putdata );
	}
}

if ( $app->isSite() && 'com_api' == JRequest::getVar( 'option' ) ) {
	JRequest::setVar( 'format', 'raw' );
}
