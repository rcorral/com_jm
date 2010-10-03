<?php

function ApiBuildRoute( &$query )
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}

	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function ApiParseRoute( $segments )
{
	$vars = array();

	$vars['view'] = 'api';

	return $vars;
}