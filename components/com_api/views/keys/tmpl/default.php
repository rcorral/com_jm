<?php
defined('_JEXEC') or die('Restricted access');
?>

<h1 class="componentheading"><?php echo JText::_('COM_API_COMPONENT_HEADING');?></h1>

<h2 class="contentheading"><?php echo JText::_('COM_API_ACCOUNT_PAGE_TITLE');?></h2>

<h3><?php echo JText::_('COM_API_REGISTERED_KEYS');?></h3>

<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
		<td class="sectiontableheader">&nbsp;</td>
		<td class="sectiontableheader"><?php echo JText::_('COM_API_DOMAIN');?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_API_KEY');?></td>
		<td class="sectiontableheader"><?php echo JText::_('COM_API_ENABLED');?></td>
	</tr>
	<?php for($i=0; $i<count($this->tokens); $i++) :
		$t		= $this->tokens[$i];
		$class 	= $i%2 ? 'sectiontableentry2' : 'sectiontableentry1';
		$img	= $t->enabled ? 'tick.png' : 'publish_x.png';
	?>
		<tr class="<?php echo $class;?>">
			<td class="api_table_count"><?php echo $i+1;?></td>
			<td class="api_table_domain"><?php echo $t->domain;?></td>
			<td class="api_table_key"><?php echo $t->hash;?></td>
			<td class="api_table_enabled"><img src="<?php echo JURI::root()."administrator/images/".$img;?>" /></td>
		</tr>
	<?php endfor; ?>
</table>

<a class="api_new_token" href="<?php echo $this->new_token_link;?>"><?php echo JText::_('COM_API_NEW_KEY');?></a>