<?php
/**
 * @copyright	Copyright (C) 2012 Red Evolution. All rights reserved.
 * @license
 */

defined('_JEXEC') or die;

/**
 * Siteplan master display controller.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 */
class SiteplanController extends JControllerLegacy
{
	/**
	 * @var		string	The default view.
	 */
	protected $default_view = 'map';

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 */
	public function display($cachable = false, $urlparams = false)
	{


		$view		= JRequest::getCmd('view', 'map');
		$layout 	= JRequest::getCmd('layout', 'default');
		$id			= JRequest::getInt('id');

		parent::display();
	}
}
