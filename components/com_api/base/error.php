<?php

class ApiError extends JError
{
	function raiseError($code, $msg)
	{
		jimport('joomla.error.exception');

		$info = null;
		$backtrace = false;

		// build error object
		throw new Exception($msg, $code);
		exit();
	}
}