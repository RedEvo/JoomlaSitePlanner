<?php
/**
 * @copyright	Copyright (C) 2012 Redevolution. All rights reserved.
 * @license
 */

defined('_JEXEC') or die;

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * View class for a list of siteplanion links.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @since		2.4
 */
class SiteplanViewMap extends JViewLegacy
{

	/**
	 * Display the view
	 *
	 */
	public function display($tpl = null)
	{

		$doc = JFactory::getDocument();
		$doc->setMetaData( 'X-UA-Compatible', 'IE=8,chrome=IE8',true ); //needed to display in IE
		JFactory::getApplication()->JComponentTitle="<div class='pagetitle '><h2>Site Plan</h2></div>";

		$css_file='/components/com_siteplan/css/siteplan.css';
		$js_file='/components/com_siteplan/js/siteplan.js';
		//if(JFile::exists(JPATH_SITE.$css_file)){
			$doc->addStyleSheet(JURI::root().$css_file);
			$doc->addScript(JURI::root().$js_file);
		//}

$map=new SiteplanBuildmap();
        $this->data= new stdClass();

		$this->data->map=$map->showMap($this->get("Map"));

		parent::display($tpl);
	}


}
