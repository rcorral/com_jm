<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Rafael Corral
 * @link 	http://www.corephp.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JApplicationFake extends JSite
{
	function __construct( $config )
	{
		parent::__construct( $config );
	}

	function getInstance( $config = array() )
	{
		static $instance;

		if ( !$instance ) {
			$instance = new JApplicationFake( $config );
		}

		return $instance;
	}

	function redirect(){}
}
