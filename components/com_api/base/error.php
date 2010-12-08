<?php

class ApiError extends JError
{
	function raiseError($code, $msg, $info = null, $backtrace = false)
	{
		jimport('joomla.error.exception');

		$exception = new JException($msg, $code, E_ERROR, $info, $backtrace);

		JResponse::setHeader('status', $exception->code.' '.str_replace( "\n", ' ', $exception->message ));
		JResponse::setBody(json_encode($exception));

		echo JResponse::toString();
		exit();
	}
}