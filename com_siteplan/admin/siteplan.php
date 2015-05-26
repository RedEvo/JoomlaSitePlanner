<?php
/**
 * @package		Site Planner
 * @copyright	Copyright (C) 2012 Red Evolution Ltd. All rights reserved.
 * @license		
 * @since		2.5
 */

defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_siteplan')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
require_once JPATH_COMPONENT_SITE.'/helpers/buildmap.php';

$controller	= JControllerLegacy::getInstance('Siteplan');
$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
