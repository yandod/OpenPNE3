<?php
// this check prevents access to debug front conrollers that are deployed by accident to production servers.
// feel free to remove this, extend it or make something more sophisticated.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1')))
{
  die('Your are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('ktai', 'dev', true);
sfContext::createInstance($configuration)->dispatch();
