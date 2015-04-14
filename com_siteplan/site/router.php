<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_Siteplan
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @param	array
 * @return	array
 */
function SiteplanBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view'])) {
		unset($query['view']);
	}

	return $segments;
}

/**
 * @param	array
 * @return	array
 */
function SiteplanParseRoute($segments)
{
	$vars = array();

	$vars['view'] = 'Siteplan';

	return $vars;
}
