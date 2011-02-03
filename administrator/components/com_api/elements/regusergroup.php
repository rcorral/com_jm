<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('JPATH_BASE') or die();

class JElementRegUserGroup extends JElement
{
	/**
	* Element name
	*
	* @access	protected
	* @var		string
	*/
	var	$_name = 'regusergroup';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$acl	=& JFactory::getACL();
		$gtree	= $acl->get_group_children_tree( null, 'USERS', false );
		$ctrl	= $control_name .'['. $name .']';

		$attribs	= ' ';
		if ($v = $node->attributes('size')) {
			$attribs	.= 'size="'.$v.'"';
		}
		if ($v = $node->attributes('class')) {
			$attribs	.= 'class="'.$v.'"';
		} else {
			$attribs	.= 'class="inputbox"';
		}

		$gtree[0]->disable = true;
		
		return JHTML::_('select.genericlist',   $gtree, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
	}
}
