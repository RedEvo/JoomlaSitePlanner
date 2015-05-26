<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @copyright	Copyright (C) 2012 Red Eevolution. All rights reserved.
 * @license		
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');



/**
 * Methods supporting siteplan map.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @since		2.4
 */
class SiteplanModelMap extends JModelList


{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	public function getMap(){
		$map=new SiteplanBuildmap();
		$this->_item[0]=$map->createMap();
		return $this->_item[0];
	}
}
