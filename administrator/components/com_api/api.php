<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

$frontside = JPATH_SITE.'/components/com_api';

JLoader::register('APIController', $frontside.'/libraries/controller.php');
JLoader::register('ApiControllerAdmin', $frontside.'/libraries/admin_controller.php');
JLoader::register('APIModel', $frontside.'/libraries/model.php');
JLoader::register('APIView', $frontside.'/libraries/view.php');

$view	= JRequest::getCmd('view', '');
$c		= JRequest::getCmd('c', '');
if ($view && !$c) :
	$c	= $view;
endif;

$c_path	= JPATH_COMPONENT_ADMINISTRATOR.'/controllers/'.strtolower($c).'.php';

if (file_exists($c_path)) :
	include_once $c_path;
	$c_name	= 'ApiController'.ucwords($c);
else :
	$c_name = 'ApiControllerAdmin';
endif;

$command = JRequest::getCmd('task', 'display');

$controller = new $c_name();
$controller->execute($command);
$controller->redirect();