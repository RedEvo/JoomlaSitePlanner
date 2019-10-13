<?php
defined('_JEXEC') or die;
/* The following line loads the MooTools JavaScript Library */
JHtml::_('behavior.framework');
/* The following line gets the application object for things like displaying the site name */
$app = JFactory::getApplication();

// Remove Scripts
$doc = JFactory::getDocument();
unset($doc->_scripts[JURI::root(true) . '/media/jui/js/bootstrap.min.js']);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <jdoc:include type="head" />
  <!-- <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/bootstrap.min.js"></script> -->
  <link href='http://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'>

  <link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/template.css" type="text/css" media="screen" />

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>

<body>

  <div class="jsp-header">
    <div class="jsp-header-col">
      <img class="jsp-logo pull-left" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/images/Joomla_site_planner_logo.svg" alt="Joomla Site Planner" title="Joomla Site Planner" />
      <ul class="btn-header pull-right">
        <li><a href="index.php" target="_self">Back to site</a></li>
      </ul>
    </div>
    <div class="clearfix"></div>
  </div>

  <div class="jsp-content">
    <div class="jsp-content-col">
        <jdoc:include type="component" />
    </div>
  </div>

  <div class="jsp-footer">
    <div class="jsp-footer-col">
      <p>Designed by <a href="http://www.redevolution.com" target="_blank">Red Evolution</a></p>
    </div>
  </div>
<!-- INCLUDE FOR MENU COLLAPSE TO WORK SMOOTHLY, STOPS MOOTOOLS AND JQUERY CONFLICTING -->
<!-- <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/js/conflict-code.js"></script> -->

</body>
</html>
