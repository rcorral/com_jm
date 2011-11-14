<?php
/**
 * @package	JM
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

$front_end = JPATH_SITE .DS. 'components' .DS. 'com_jm';

JLoader::register( 'JMController', $front_end .DS. 'libraries' .DS. 'controller.php' );
JLoader::register( 'JMControllerAdmin',
	$front_end .DS. 'libraries' .DS. 'admin' .DS. 'controller.php' );
JLoader::register( 'JMModel', $front_end .DS. 'libraries' .DS. 'model.php' );
JLoader::register( 'JMView', $front_end .DS. 'libraries' .DS. 'view.php' );

$view       = JRequest::getCmd( 'view', '' );
$controller = JRequest::getCmd( 'c', '' );
if ( $view && !$controller ) {
	$controller	= $view;
}

$c_path	= JPATH_COMPONENT_ADMINISTRATOR .DS. 'controllers' .DS. strtolower( $controller ) . '.php';

if ( file_exists( $c_path ) ) {
	include_once $c_path;
	$c_name	= 'JMController' . ucwords( $controller );
} else {
	$c_name = 'JMControllerAdmin';
}

$controller = new $c_name();
$controller->execute( JRequest::getCmd( 'task', 'display' ) );
$controller->redirect();