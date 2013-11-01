<?php
require_once(dirname(__FILE__) . '/../config/config.php');

if (!empty($plexWatch['myPlexAuthToken'])) {
	$url = $_GET['img']."&X-Plex-Token=".$plexWatch['myPlexAuthToken']."";
}else{
	$url = $_GET['img'];
}

$img = file_get_contents($url);

header("Content-Type: image/jpg");

echo $img;
?>