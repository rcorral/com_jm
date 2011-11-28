<?php
/**
 * @package	JM
 * @version 1.5
 * @author 	Rafael Corral
 * @link 	http://www.corephp.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

class JMHelper
{
	function getJMUserID()
	{
		static $user_id;

		if ( !$user_id ) {
			$user_id = JMAuthentication::getInstance()->authenticate();
		}

		return $user_id;
	}

	function setSessionUser( $user_id = false )
	{
		if ( false === $user_id ) {
			$user_id = self::getJMUserID();
		}

		$session =& JFactory::getSession();
		$session->set( 'user', JUser::getInstance( $user_id ) );
	}

	function unsetSessionUser()
	{
		$session  =& JFactory::getSession();
		$session->clear( 'user' );
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user   = JFactory::getUser();
		$result = new JObject;

		$assetName = 'com_jm';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ( $actions as $action ) {
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}

		return $result;
	}

	public function getField( $type, $attributes = array(), $field_value = '' )
	{
		static $types = null;

		$defaults = array( 'name' => '', 'id' => '' );

		if ( !$types ) {
			jimport('joomla.form.helper');
			$types = array();
		}

		if ( !in_array( $type, $types ) ) {
			JFormHelper::loadFieldClass( $type );
		}

		try {
			$attributes = array_merge( $defaults, $attributes );

			$xml = new JXMLElement( '<?xml version="1.0" encoding="utf-8"?><field />' );
			foreach ( $attributes as $key => $value ) {
				if ( '_options' == $key ) {
					foreach ( $value as $_opt_value ) {
						$xml->addChild( 'option', $_opt_value->text )
							->addAttribute( 'value', $_opt_value->value );
					}
					continue;
				}
				$xml->addAttribute( $key, $value );
			}

			$class = 'JFormField' . $type;
			$field = new $class();
			$field->setup( $xml, $field_value );

			return $field;
		} catch( Exception $e ) {
			return false;
		}
	}
}