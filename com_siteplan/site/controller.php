<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_siteplan
 * @copyright	Copyright (C) 2012 Red Evolution Ltd, Inc. All rights reserved.
 * @license
 */

defined('_JEXEC') or die;

/**
 * Content Component Controller
 *
 * @package		Joomla.Site
 * @subpackage	com_siteplan
 * @since		2.5
 */
class SiteplanController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	2.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;

		// Set the default view name and format from the Request.
		$vName		= JRequest::getCmd('view', 'map');
		JRequest::setVar('view', $vName);

		return parent::display($cachable, array('Itemid'=>'INT'));
	}
}
