<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

$frontside = JPATH_SITE.'/components/com_api';

include_once $frontside.'/base/controller.php';
include_once $frontside.'/base/model.php';
include_once $frontside.'/base/view.php';

$view	= JRequest::getCmd('view', '');
if ($view) :
	$c	= $view;
endif;

$c_path	= JPATH_COMPONENT.'/controllers/'.strtolower($c).'.php';
if (file_exists($c_path)) :
	include_once $c_path;
	$c_name	= 'ApiController'.ucwords($c);
else :
	$c_name = 'ApiController';
endif;

$command = JRequest::getCmd('task', 'display');

$controller = new $c_name();
$controller->execute($command);
$controller->redirect();