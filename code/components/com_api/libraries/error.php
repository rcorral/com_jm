<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

class ApiError extends JError
{
	function raiseError($code, $msg, $info = null, $backtrace = false)
	{
		jimport('joomla.error.exception');

		$exception = new JException($msg, $code, E_ERROR, $info, $backtrace);

		JResponse::setHeader('status', $exception->code);
		JResponse::setBody(json_encode($exception));

		echo JResponse::toString();
		exit();
	}
}