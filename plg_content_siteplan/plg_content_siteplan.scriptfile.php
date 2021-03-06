<?php
/**
/**
 * @package		Site Planner
 * @copyright	Copyright (C) 2012 Red Evolution Ltd. All rights reserved.
 * @license GNU / GPL 
 * @since		2.5
 *
**/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Script file of siteplan content plugin
 */
class plgContentSiteplanInstallerScript
{ 
  function install($parent) { 
     // I activate the plugin
	$db = JFactory::getDbo();
     $tableExtensions = $db->quoteName("#__extensions");
     $columnElement   = $db->quoteName("element");
     $columnType      = $db->quoteName("type");
     $columnEnabled   = $db->quoteName("enabled");
     
     // Enable plugin
     $db->setQuery("UPDATE $tableExtensions SET $columnEnabled=1 WHERE $columnElement='siteplan' AND $columnType='plugin'");
     $db->query();
     
     echo '<p>'. JText::_('PLG_CONTENT_SITEPLAN_PLUGIN_ENABLED') .'</p>';    
  } 
}
?>