<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_siteplan
 * @copyright	Copyright (C) 2012 Redevolution. All rights reserved.
 * @license		
 */

// No direct access.
defined('_JEXEC') or die;

$user		= JFactory::getUser();
?>

<form method="post" name="adminForm" id="adminForm">
	<div class="clr"> </div>
	<?php
		echo $this->data->map;
	?>
</form>
