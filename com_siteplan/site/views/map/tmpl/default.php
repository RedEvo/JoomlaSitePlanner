<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @copyright	Copyright (C) 2012 Red Evolution Ltd. All rights reserved.
 * @license
 */

// No direct access.
defined('_JEXEC') or die;

$user		= JFactory::getUser();
?>
<ul class="jsp-key">
	<li class="jsp-key-title">Key:</li>
	<li class="jsp-required">Needed</li>
	<li class="jsp-not-required">Incomplete</li>
	<li class="jsp-present">Done</li>
</ul>
<form method="post" name="adminForm" id="adminForm">
	<div class="clr"> </div>
	<?php
        //$this->data->showMap();
		echo $this->data->map;
	?>
</form>
