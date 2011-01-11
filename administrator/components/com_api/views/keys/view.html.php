<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ApiViewKeys extends ApiView {
	
	public function display($tpl = null) {
		
		$this->generateToolbar();
		
		$model		= $this->getModel();
		$rows		= $this->get('list');
		$pagination	= $this->get('pagination');
		
		$this->assignRef('model', $model);
		$this->assignRef('rows', $rows);
		$this->assignRef('pagination', $pagination);
		
		parent::display($tpl);
	}
	
	private function generateToolbar() {
		JToolBarHelper::title(JText::_('COM_API').': '.JText::_('COM_API_KEYS'));
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::addNewX();
		JToolBarHelper::editListX();
		JToolBarHelper::deleteListX();
	}
	
}
