<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
include_once JPATH_COMPONENT.'/controllers/api.php';

JModel::addIncludePath(JPATH_COMPONENT.'/models');
JTable::addIncludePath(JPATH_COMPONENT.'/tables');

$command 	= JRequest::getCmd('task', 'display');

$controller = new ApiControllerMain();
$controller->execute($command);
$controller->redirect();