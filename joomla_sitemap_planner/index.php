<?php
defined('_JEXEC') or die;

/* The following line loads the MooTools JavaScript Library */
JHtml::_('behavior.framework', true);

/* The following line gets the application object for things like displaying the site name */
$app = JFactory::getApplication();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  
  <meta charset="utf-8">
  <jdoc:include type="head" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  
  <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/bootstrap.min.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/bootstrap-responsive.min.css" type="text/css" media="screen" />
  <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/custom_bootstrap.css" type="text/css" media="screen" />
  
</head>

<body >
  
  <div class="container">
    <div class="row">
      <div class="span12">
        <img class="logo" width="282" height="35" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/Joomla_site_planner_logo.png" alt="Joomla Site Planner" title="Joomla Site Planner" />
        <ul class="nav nav-pills pull-right">
          <li class="active pull-right"><a href="index.php" target="_self">Back</a></li>
        </ul>
      </div>
    </div>
  </div>
  
  <div class="container">

    <div class="row">
      <div class="span12">
        <jdoc:include type="component" />
      </div>
    </div>
    
    <div class="row footer">
      <div class="span12">
        <p>Designed by <a href="http://www.redevolution.com" target="_blank">Red Evolution</a></p>  
      </div>
    </div>
    
  </div>   
    
<!-- Placed at the end of the document so the pages load faster -->
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js" type="text/javascript"></script>-->
<script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/bootstrap.min.js"></script>
  
</body>
</html>