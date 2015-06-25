<?php
require_once(dirname(__FILE__) . '/ConfigClass.php');
$config_file = dirname(__FILE__) . '/../config/config.php';
if (file_exists($config_file)) {
	$config = new ConfigClass($config_file);
} else {
	$config = new ConfigClass();
}
$config->save();
?>