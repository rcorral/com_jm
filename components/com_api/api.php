<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

include_once JPATH_COMPONENT.'/base/controller.php';
include_once JPATH_COMPONENT.'/base/model.php';
include_once JPATH_COMPONENT.'/base/plugin.php';
include_once JPATH_COMPONENT.'/base/view.php';
include_once JPATH_COMPONENT.'/base/error.php';

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
	JError::raiseError(404, JText::_('API_PLUGIN_CONTROLLER_NOT_FOUND'));
endif;

$command = JRequest::getCmd('task', 'display');

$controller = new $c_name();
$controller->execute($command);
$controller->redirect();