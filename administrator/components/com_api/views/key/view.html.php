<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ApiViewKey extends ApiView {
	
	public function display($tpl = null) {
		$this->generateToolbar();
		
		$model		= $this->getModel();
		$row		= $this->get('data');
		
		$return		= 'index.php?option='.$this->option.'&view=keys';
		
		$this->assignRef('return', $return);
		$this->assignRef('model', $model);
		$this->assignRef('row', $row);
		
		parent::display($tpl);
	}
	
	private function generateToolbar() {
		JToolBarHelper::title(JText::_('COM_API').': '.JText::_('COM_API_KEYS'));
		JToolBarHelper::save();
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
	}
	
}
