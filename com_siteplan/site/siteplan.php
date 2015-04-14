<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_wrapper
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/buildmap.php';


$controller = JControllerLegacy::getInstance('Siteplan');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();
