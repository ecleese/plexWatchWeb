<?php
require_once(dirname(__FILE__) . '/../config/config.php');

if (isset($_GET['img']) && (substr($_GET['img'], 0, 4) == 'http')) {
	$img = $_GET['img'];
} else {
	$img = '';
}
if (!empty($plexWatch['myPlexAuthToken'])) {
	$url = $img."&X-Plex-Token=".$plexWatch['myPlexAuthToken']."";
} else {
	$url = $img;
}
$img = file_get_contents($url);
header("Content-Type: image/jpg");
echo $img;
?>