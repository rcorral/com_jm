<?php
defined('_JEXEC') or die('Restricted access');

JFactory::getDocument()->addScriptDeclaration("
	function submitbutton(pressbutton) {
		if (pressbutton == 'save') {
			var domain = document.adminForm.domain.value;
			if (domain == '') {
				alert('".JText::_("COM_API_INVALID_DOMAIN_MSG")."');
				return false;
			}
		}
		submitform(pressbutton);
	}
");

?>

<h1 class="componentheading"><?php echo JText::_('COM_API_COMPONENT_HEADING');?></h1>
<h2 class="contentheading"><?php echo JText::_('COM_API_NEW_KEY_PAGE_TITLE');?></h2>
<form action="index.php" method="post" name="adminForm" class="api_key_form">
	<p>
		<label class="api_form_label" for="domain"><?php echo JText::_('COM_API_DOMAIN');?></label>
		<input type="text" class="inputbox api_form_input" name="domain" size="55" />
		<?php echo JHTML::tooltip(JText::_('COM_API_DOMAIN_TOOLTIP'), JText::_('COM_API_DOMAIN')); ?>
	</p>
	<p>
		<input type="submit" name="submit" value="Submit" onclick="return submitbutton('save');" />
		<input type="submit" name="cancel" value="Cancel" onclick="return submitbutton('cancel');" />
	</p>
	<input type="hidden" name="option" id="option" value="com_api" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="c" id="c" value="keys" />
	<input type="hidden" name="return" value="<?php echo $this->return;?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>