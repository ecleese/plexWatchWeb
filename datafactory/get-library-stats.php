<?php
require_once(dirname(__FILE__) . '/../includes/functions.php');

$database = dbconnect();
$query = "SELECT count(DISTINCT user) as users FROM processed";
$results = getResults($database, $query);
$users = $results->fetchColumn();

$PMSdieMsg = '<div class=\"alert alert-warning \">Failed to access Plex Media ' .
	'Server. Please check your settings.</div>';

$sectionsData = getPmsData('/library/sections');
$sections = simplexml_load_string($sectionsData) or die ($PMSdieMsg);
echo '<ul>';
	echo '<li><h1>'. $users . '</h1><h5>Users</h5></li>';
	foreach ($sections->children() as $section) {
		if (($section['type'] == 'movie') || ($section['type'] == 'artist'))  {
			$sectionDetails = simplexml_load_string(getPmsData('/library/sections/' .
				$section['key'] . '/all?X-Plex-Container-Start=0&X-Plex-Container-Size=0'))
				or die ($PMSdieMsg);
			echo '<li>';
				echo '<h1>' . $sectionDetails['totalSize'] . '</h1>';
				echo '<h5>' . $section['title'] . '</h5>';
			echo '</li>';
		} else if ($section['type'] == "show") {
			$sectionDetails = simplexml_load_string(getPmsData('/library/sections/' .
				$section['key'] . '/all?type=2&X-Plex-Container-Start=0&X-Plex-Container-Size=0'))
				or die ($PMSdieMsg);
			$tvEpisodeCount = simplexml_load_string(getPmsData('/library/sections/' .
				$section['key'] . '/all?type=4&X-Plex-Container-Start=0&X-Plex-Container-Size=0'))
				or die ($PMSdieMsg);

			// Cut ' Shows' from the end of the title
			if (strlen($section['title']) > 6 && strcmp(' Shows', substr($section['title'], -6)) == 0) {
				$title = substr($section['title'], 0, -6);
			} else {
				$title = $section['title'];
			}
			echo '<li>';
				echo '<h1>' . $sectionDetails['totalSize'] . '</h1>';
				echo '<h5>' . $title . ' Shows</h5>';
			echo '</li>';
			echo '<li>';
				echo '<h1>' . $tvEpisodeCount['totalSize'] . '</h1>';
				echo '<h5>' . $title . ' Episodes</h5>';
			echo '</li>';
		}
	}
echo '</ul>';
?>