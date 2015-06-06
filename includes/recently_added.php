<?php
date_default_timezone_set(@date_default_timezone_get());

require_once(dirname(__FILE__) . '/../config/config.php');
require_once(dirname(__FILE__) . '/timeago.php');

if (isset($_GET['width'])) {
	$containerSize = 1; // min size?
	$tmp = intval($_GET['width'])/182;
	if ($tmp > 0) {
		$containerSize = $tmp;
		if (!isset($singlerow)) {
			$containerSize = $containerSize*1;
		}
	}
	$containerSize = floor($containerSize);
}

$fileContents = getPmsData('/library/recentlyAdded?X-Plex-Container-Start=0' .
	'&X-Plex-Container-Size=' .	$containerSize) or
	die ("<div class='alert alert-warning'>Failed to access Plex Media Server. " .
		"Please check your settings.</div>");
$recentRequest = simplexml_load_string($fileContents);

echo "<div class='dashboard-recent-media-row'>";
	echo "<ul class='dashboard-recent-media'>";
		// Run through each feed item
		foreach ($recentRequest->children() as $recentXml) {
			$recentThumbUrl = $recentXml['thumb'].'&width=153&height=225';
			echo "<div class='dashboard-recent-media-instance'>";
				echo "<li>";
					echo "<div class='poster'><div class='poster-face'>";
						echo "<a href='info.php?id=".$recentXml['ratingKey']."'>";
						if ($recentXml['thumb']) {
							echo "<img src='includes/img.php?img=".urlencode($recentThumbUrl).
								"' class='poster-face'>";
						} else {
							echo "<img src='images/poster.png' class='poster-face'>";
						}
						echo "</a>";
					echo "</div></div>";
					echo "<div class=dashboard-recent-media-metacontainer>";
						if ($recentXml['type'] == "season") {
							echo "<h3>Season ".$recentXml['index']."</h3>";
						} else if ($recentXml['type'] == "movie") {
							echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";
						}
						$recentTime = $recentXml['addedAt'];
						$timeNow = time();
						$age = time() - strtotime($recentTime);
						echo "<h4>Added ".TimeAgo($recentTime)."</h4>";
					echo "</div>";
				echo "</li>";
			echo "</div>";
		}
	echo "</ul>";
echo "</div>";
?>