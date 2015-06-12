<?php
require_once(dirname(__FILE__) . '/ConfigClass.php');

$config = new ConfigClass();
$config->save();

// Send the user back to the form
header('Location: ../settings.php?s=' . urlencode('Settings saved.'));
exit;
?>