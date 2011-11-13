<?php
/**
 * @package	JM
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/
defined('_JEXEC') or die('Restricted access');

JFactory::getDocument()->addScriptDeclaration("
	function submitbutton(pressbutton) {
		if (pressbutton == 'save') {
			var domain = document.adminForm.domain.value;
			var regex_sanitize = /(http|https|ftp):\/\//i
			var sanitized = domain.replace(regex_sanitize, '');
			var regex_validate = /^([0-9a-z-_\.]+\.+[0-9a-z\.])+|localhost$/i;
			if (regex_validate.test(sanitized) == false) {
				alert('".JText::_("COM_JM_INVALID_DOMAIN_MSG")."');
				return false;
			}
		}
		submitform(pressbutton);
	}
");

?>

<h1 class="componentheading"><?php echo JText::_('COM_JM_COMPONENT_HEADING');?></h1>
<h2 class="contentheading"><?php echo $this->key->id ? JText::_('COM_JM_EDIT_KEY_PAGE_TITLE') : JText::_('COM_JM_NEW_KEY_PAGE_TITLE');?></h2>
<form action="index.php" method="post" name="adminForm" class="jm_key_form">
	<p>
		<label class="jm_form_label" for="domain"><?php echo JText::_('COM_JM_DOMAIN');?>:</label>
		<input type="text" class="inputbox jm_form_input" name="domain" size="55" value="<?php echo $this->key->domain;?>" />
		<?php echo JHTML::tooltip(JText::_('COM_JM_DOMAIN_TOOLTIP'), JText::_('COM_JM_DOMAIN')); ?>
	</p>
	<?php if ($this->key->hash) : ?>
		<p>
			<label class="jm_form_label"><?php echo JText::_('COM_JM_KEY');?>:</label>
			<span class="jm_form_key"><?php echo $this->key->hash;?></span>
		</p>
	<?php endif; ?>
	<p>
		<input type="submit" name="submit" value="Submit" onclick="return submitbutton('save');" />
		<input type="submit" name="cancel" value="Cancel" onclick="return submitbutton('cancel');" />
	</p>
	<input type="hidden" name="option" id="option" value="com_jm" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="id" id="id" value="<?php echo $this->key->id;?>" />
	<input type="hidden" name="c" id="c" value="keys" />
	<input type="hidden" name="return" value="<?php echo $this->return;?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>