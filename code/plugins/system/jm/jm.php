<?php
/**
 * @package	JM
 * @version 0.2
 * @author 	Rafael Corral
 * @link 	http://jommobile.com
 * @copyright Copyright (C) 2012 Rafael Corral. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

class plgSystemJM extends JPlugin
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
	$putdata = plgSystemJM::getPutParameters(file_get_contents('php://input'));
	$putdata['format'] = 'raw';

	if ( isset( $putdata['option'] ) && 'com_jm' == $putdata['option'] ) {
		$_REQUEST = array_merge( $_REQUEST, $putdata );
		$_POST = array_merge( $_POST, $putdata );
	}
}

if ( $app->isSite() && 'com_jm' == JRequest::getVar( 'option' ) ) {
	JRequest::setVar( 'format', 'raw' );
}
