<?php
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

include_once JPATH_COMPONENT.'/base/controller.php';
include_once JPATH_COMPONENT.'/base/model.php';
include_once JPATH_COMPONENT.'/base/plugin.php';
include_once JPATH_COMPONENT.'/controllers/main.php';

$command 	= JRequest::getCmd('task', 'dispatch');

$controller = new ApiControllerMain();
$controller->execute($command);
$controller->redirect();