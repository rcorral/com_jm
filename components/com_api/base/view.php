<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class ApiView extends JView {

	protected $option	= null;
	protected $view		= null;

	function __construct() {
		$this->set('option', JRequest::getCmd('option'));
		parent::__construct();
	}

	public function display($tpl = null) {
		$app	= JFactory::getApplication();
		if ($app->isAdmin()) :
			$this->generateSubmenu();
		endif;
		
		parent::display($tpl);
	}
	
	private function generateSubmenu() {
		$views = $this->getMainViews();
	
		foreach($views as $item) :
			$link = 'index.php?option='.$this->get('option').'&view='.$item['view'];
			JSubMenuHelper::addEntry($item['name'], $link, ($this->get('view') == $item['view']));
		endforeach;
	
	}
	
	protected function getMainViews() {
		$views = array(
					array('name' => 'Control Panel', 'view' => 'cpanel'),
				);
		return $views;
	}
	
	protected function routeLayout($tpl) {
		$layout = ucwords(strtolower($this->getLayout()));
		
		if ($layout == 'Default') :
			return false;
		endif;
		
		$method_name = 'display'.$layout;
		if (method_exists($this, $method_name) && is_callable(array($this, $method_name))) :
			$this->$method_name($tpl);
			return true;
		else :
			$this->setLayout('default');
			return false;
		endif;
		
	}
}
