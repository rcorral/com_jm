<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

JLoader::register('APIController', JPATH_COMPONENT.'/libraries/controller.php');
JLoader::register('APIModel', JPATH_COMPONENT.'/libraries/model.php');
JLoader::register('APIView', JPATH_COMPONENT.'/libraries/view.php');
JLoader::register('APIPlugin', JPATH_COMPONENT.'/libraries/plugin.php');
JLoader::register('APIError', JPATH_COMPONENT.'/libraries/error.php');
JLoader::register('APIAuthentication', JPATH_COMPONENT.'/libraries/authentication.php');
JLoader::register('APIAuthenticationKey', JPATH_COMPONENT.'/libraries/authenticationkey.php');

$view	= JRequest::getCmd('view', '');
if ($view) :
	$c	= $view;
else :
	$c	= JRequest::getCmd('c', 'http');
endif;

$c_path	= JPATH_COMPONENT.'/controllers/'.strtolower($c).'.php';
if (file_exists($c_path)) :
	include_once $c_path;
	$c_name	= 'ApiController'.ucwords($c);
else :
	JError::raiseError(404, JText::_('COM_API_CONTROLLER_NOT_FOUND'));
endif;

$command = JRequest::getCmd('task', 'display');

$controller = new $c_name();
$controller->execute($command);
$controller->redirect();