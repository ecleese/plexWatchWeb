<?php
date_default_timezone_set(@date_default_timezone_get());

$guisettingsFile = dirname(__FILE__) . '/config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
} else {
	header('Location: settings.php');
}

$database = dbconnect();
$itemId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT,
	array('options'=>array('min_range'=>1)));
if (!isset($itemId) || $itemId === false) {
	echo "<p>ID field is required.</p>";
	$error_msg = 'PlexWatchWeb :: POST parameter "id" not found or invalid.';
	trigger_error($error_msg, E_USER_ERROR);
}
$infoPath = '/library/metadata/' . $itemId;

function printMetadata($xml) {
	$data = &metaDataData($xml);
	echo '<div class="container-fluid">';
		if ($data['span12']) {
			echo '<div class="row-fluid">';
				echo '<div class="span12">';
		}
				if ($data['xmlArt']) {
					echo '<div class="art-face" ' .
						'style="background-image:url(' . $data['xmlArtUrl'] . ')">';
				} else {
					echo '<div class="art-face">';
				}
					echo '<div class="summary-wrapper">';
						echo '<div class="summary-overlay">';
							echo '<div class="row-fluid">';
								if ($data['type'] == 'show') {
									echo '<div class="span12">';
								} else {
									echo '<div class="span9">';
								}
									echo '<div class="summary-content-poster hidden-phone hidden-tablet">';
										echo '<img src="' . $data['xmlThumbUrl'] . '">';
									echo '</div>';
									echo '<div class="summary-content">';
										echo '<div class="summary-content-title">';
											echo '<h1>' . $data['title'] . '</h1>';
										echo '</div>';
										if ($data['type'] == 'movie') {
											echo '<div class="rateit hidden-phone hidden-tablet" ' .
												'data-rateit-value="' . $data['starRating'] .
												'" data-rateit-ispreset="true" data-rateit-readonly="true"></div>';
										}
										echo '<div class="summary-content-details-wrapper">';
											echo '<div class="summary-content-director">';
												echo $data['director'];
											echo '</div>';
											echo '<div class="summary-content-duration">';
												echo 'Runtime <strong>' . $data['durationRounded'] .
													' mins</strong>';
											echo '</div>';
											echo '<div class="summary-content-content-rating">';
												echo 'Rated <strong>'.$data['rating'].'</strong>';
											echo '</div>';
										echo '</div>';
										echo '<div class="summary-content-summary">';
											echo '<p>' . $data['summary'] . '</p>';
										echo '</div>';
									echo '</div>'; // .summary-content
								echo '</div>'; // .span9 (show: .span12)
								if ($data['type'] == 'episode') {
									printEpisodeWriters($xml);
								} else if ($data['type'] == 'movie') {
									printMoviePeople($xml);
								} else if ($data['type'] == 'season') {
									echo '<div class="span3"></div>';
								}
							echo '</div>'; // .row-fluid
						echo '</div>'; // .summary-overlay
					echo '</div>'; // .summary-wrapper
				echo '</div>'; // .art-face
		if ($data['span12']) {
				echo '</div>'; // .span12
			echo '</div>'; // .row-fluid
		}
	echo '</div>'; // .container-fluid
}

function metaDataData($xml) {
	$imgBase = 'includes/img.php?img=';
	$data = array();
	if ($xml->Video['type'] == 'episode') {
		$data = episodeMetaData($xml);
	} else if ($xml->Directory['type'] == 'show') {
		$data = showMetaData($xml);
	} else if ($xml->Directory['type'] == 'season') {
		$data = seasonMetaData($xml);
	} else if ($xml->Video['type'] == 'movie') {
		$data = movieMetaData($xml);
	}
	$durationMinutes = $data['duration'] / 1000 / 60;
	$data['durationRounded'] = floor($durationMinutes);
	$data['xmlArtUrl'] = $imgBase .
		urlencode($data['xmlArt'] . '&width=1920&height=1080');
	$data['xmlThumbUrl'] = 'images/poster.png';
	if (isset($data['xmlThumb'])) {
		$data['xmlThumbUrl'] = $imgBase .
			urlencode($data['xmlThumb'] . '&width=256&height=352');
	}
	if (($data['type'] == 'episode') || ($data['type'] == 'movie')) {
		$data['span12'] = true;
	} else {
		$data['span12'] = false;
	}
	return $data;
}

function episodeMetaData($xml) {
	$data = array();
	$data['type'] = 'episode';
	$data['xmlArt'] = $xml->Video['art'];
	if ($xml->Video['parentThumb']) {
		$data['xmlThumb'] = $xml->Video['parentThumb'];
	} else if ($xml->Video['grandparentThumb']) {
		$data['xmlThumb'] = $xml->Video['grandparentThumb'];
	}
	$data['title'] = $xml->Video['grandparentTitle'] .
		' (Season ' . $xml->Video['parentIndex'] . ',' .
		' Episode ' . $xml->Video['index'] . ')' .
		' "' . $xml->Video['title'] . '"';
	$data['director'] = 'Directed by <strong>' . $xml->Video->Director['tag'] .
		'</strong>';
	$data['duration'] = $xml->Video['duration'];
	$data['rating'] = $xml->Video['contentRating'];
	$data['summary'] = $xml->Video['summary'];
	return $data;
}

function showMetaData($xml) {
	$data = array();
	$data['type'] = 'show';
	$data['xmlArt'] = $xml->Directory['art'];
	$data['xmlThumb'] = $xml->Directory['thumb'];
	$data['title'] = $xml->Directory['title'];
	$data['director'] = 'Studio <strong>' . $xml->Directory['studio'] . '</strong>';
	$data['duration'] = $xml->Directory['duration'];
	$data['rating'] = $xml->Directory['contentRating'];
	$data['summary'] = $xml->Directory['summary'];
	return $data;
}

function seasonMetaData($xml) {
	$data = array();
	$data['type'] = 'season';
	$parentInfoPath = '/library/metadata/' . $xml->Directory['parentRatingKey'];
	$parentXml = simplexml_load_string(getPMSData($parentInfoPath));
	if ($parentXml === false) {
		$error_msg = 'Feed Not Found';
		echo '<p>' . $error_msg . '</p>';
		trigger_error($error_msg, E_USER_ERROR);
	}
	$data['xmlArt'] = $xml->Directory['art'];
	if ($xml->Directory['thumb']) {
		$data['xmlThumb'] = $xml->Directory['thumb'];
	} else if ($xml->Directory['parentThumb']) {
		$data['xmlThumb'] = $xml->Directory['parentThumb'];
	}
	$data['title'] = $xml->Directory['parentTitle'] .
		' (' . $xml->Directory['title'] . ')';
	$data['director'] = 'Studio <strong>' . $parentXml['studio'] . '</strong>';
	$data['duration'] = $parentXml->Directory['duration'];
	$data['rating'] = $parentXml->Directory['contentRating'];
	$data['summary'] = $parentXml->Directory['summary'];
	return $data;
}

function movieMetaData($xml) {
	$data = array();
	$data['type'] = 'movie';
	$data['xmlArt'] = $xml->Video['art'];
	$data['xmlThumb'] = $xml->Video['thumb'];
	$data['title'] = $xml->Video['title'] . ' (' . $xml->Video['year'] . ')';
	$data['starRating'] = ceil($xml->Video['rating'] / 2);
	$data['director'] = 'Directed by <strong>' . $xml->Video->Director['tag'] .
		'</strong>';
	$data['duration'] = $xml->Video['duration'];
	$data['rating'] = $xml->Video['contentRating'];
	$data['summary'] = $xml->Video['summary'];
	return $data;
}

function printEpisodeWriters($xml) {
	echo '<div class="span3">';
		echo '<div class="summary-content-people-wrapper hidden-phone hidden-tablet">';
			echo '<div class="summary-content-writers"><h6><strong>Written by</strong></h6><ul><li>';
				$writerCount = 0;
				if ($xml->Video->Writer['tag']) {
					foreach ($xml->Video->Writer as $xmlWriter) {
						$writers[] = $xmlWriter['tag'];
						if (++$writerCount == 5) {
							break;
						}
					}
					echo implode('</li><li>', $writers);
				} else {
					echo 'n/a';
				}
			echo '</li></ul></div>';
		echo '</div>';
	echo '</div>'; // .span3
}

function printHistory($xml) {
	global $itemId, $database;
	$plexWatchDbTable = dbTable();
	$clauses = "FROM $plexWatchDbTable " .
		"WHERE session_id LIKE '%/metadata/" . $itemId . "\_%' ESCAPE '\' " .
		"ORDER BY time DESC";
	$countQuery = 'SELECT COUNT(*) as count ' . $clauses;
	$results = getResults($database, $countQuery);
	$numRows = $results->fetchColumn();
	$historyQuery = 'SELECT title, user, platform, time, stopped, ip_address, ' .
		'xml, paused_counter ' . $clauses;
	$results = getResults($database, $historyQuery);
	echo '<div class="container-fluid">';
		echo '<div class="clear"></div>';
		echo '<div class="row-fluid">';
			echo '<div class="span12">';
				echo '<div class="wellbg">';
					echo '<div class="wellheader">';
						echo '<div class="dashboard-wellheader">';
							echo'<h3>Watching history for <strong>' . $xml->Video['title'] .
								'</strong> (' . $numRows . ' Views)</h3>';
						echo'</div>';
					echo'</div>'; // .wellheader
					if ($numRows < 1) {
						echo 'No Results.';
					} else {
						echo '<table id="globalHistory" class="display">';
							echo '<thead>';
								echo '<tr>';
									echo '<th align="left"><i class="fa fa-sort"></i> Date</th>';
									echo '<th align="left"><i class="fa fa-sort"></i> User</th>';
									echo '<th align="left"><i class="fa fa-sort"></i> Platform</th>';
									echo '<th align="left"><i class="fa fa-sort"></i> IP Address</th>';
									echo '<th align="center"><i class="fa fa-sort"></i> Started</th>';
									echo '<th align="center"><i class="fa fa-sort"></i> Paused</th>';
									echo '<th align="center"><i class="fa fa-sort"></i> Stopped</th>';
									echo '<th align="center"><i class="fa fa-sort"></i> Duration</th>';
									echo '<th align="center"><i class="fa fa-sort"></i> Completed</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
								$rowCount = 0;
								while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
									$rowCount++;
									printHistoryRow($row, $rowCount);
								}
							echo '</tbody>';
						echo '</table>';
					}
				echo '</div>'; // .wellbg
			echo '</div>'; // .span12
		echo '</div>'; // .row-fluid
	echo '</div>'; // .container-fluid
}

function printHistoryRow($row, $rowCount) {
	echo '<tr>';
		echo '<td data-order="' . $row['time'] . '" align="left">';
			echo $row['time'];
		echo '</td>';
		echo '<td align="left">';
			echo '<a href="user.php?user=' . $row['user'] . '">';
				echo FriendlyName($row['user'], $row['platform']);
			echo '</a>';
		echo '</td>';
		$rowXml = simplexml_load_string($row['xml']);
		$platform = $rowXml->Player['platform'];
		echo '<td align="left">';
			echo '<a href="#streamDetailsModal' . $rowCount . '" data-toggle="modal">';
				echo '<span class="badge badge-inverse">';
					echo '<i class="fa fa-info"></i>';
				echo '</span>';
			echo '</a>';
		if ($platform == 'Chromecast') {
			echo '&nbsp' . $platform . '</td>';
		} else {
			echo '&nbsp' . $row['platform'] . '</td>';
		}
		echo '<td align="left">';
			if (empty($row['ip_address'])) {
				echo 'n/a';
			} else {
				echo $row['ip_address'];
			}
		echo '</td>';
		$request_url = $row['xml'];
		$xmlfield = simplexml_load_string($request_url);
		$duration = $xmlfield['duration'];
		$viewOffset = $xmlfield['viewOffset'];
		echo '<div id="streamDetailsModal' . $rowCount .
			'" class="modal hide fade" tabindex="-1" role="dialog" '.
			'aria-labelledby="myModalLabel" aria-hidden="true">';
			echo '<div class="modal-header">';
				echo '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">';
					echo '<i class="icon icon-remove"></i>';
				echo '</button>';
				echo '<h3 id="myModalLabel">';
					echo '<i class="icon-info-sign icon-white"></i> ';
					echo 'Stream Info: <strong>' . $row['title'] . ' ('.
						FriendlyName($row['user'], $row['platform']) . ')</strong>';
				echo '</h3>';
			echo '</div>';
			echo '<div class="modal-body">';
				printStreamDetails($xmlfield);
			echo '</div>';
			echo '<div class="modal-footer"></div>';
		echo '</div>';
		echo '<td align="center">' . $row['time'] . '</td>';
		$paused_duration = round(abs($row['paused_counter']) / 60, 1);
		echo '<td align="center">' . $paused_duration . ' min</td>';
		echo '<td align="center">';
			if (empty($row['stopped'])) {
				echo 'n/a';
			} else {
				echo $row['stopped'];
			}
		echo '</td>';
		$viewed_time = round(abs($row['stopped'] - $row['time'] - $row['paused_counter']) / 60, 0);
		$viewed_time_length = strlen($viewed_time);
		echo '<td align="center">';
			if ($viewed_time_length == 8) {
				echo 'n/a';
			} else {
				echo $viewed_time . ' min';
			}
		echo '</td>';
		$percentComplete = ($duration == 0 ? 0 : sprintf('%2d', ($viewOffset / $duration) * 100));
		if ($percentComplete >= 90) {
			$percentComplete = 100;
		}
		echo '<td align="center">';
			echo '<span class="badge badge-warning">' . $percentComplete . '%</span>';
		echo '</td>';
	echo '</tr>';
}

function printMoviePeople($xml) {
	echo '<div class="span3">';
		echo '<div class="summary-content-people-wrapper hidden-phone hidden-tablet">';
			echo '<div class="summary-content-actors"><h6><strong>Genres</strong></h6><ul><li>';
				$genreCount = 0;
				if ($xml->Video->Genre['tag']) {
					foreach ($xml->Video->Genre as $xmlGenres) {
						$genres[] = $xmlGenres['tag'];
						if (++$genreCount == 5) {
							break;
						}
					}
					echo implode('</li><li>', $genres);
				} else {
					echo 'n/a';
				}
			echo '</li></ul></div>';
			echo '<div class="summary-content-actors"><h6><strong>Starring</strong></h6><ul><li>';
				$roleCount = 0;
				if ($xml->Video->Role['tag']) {
					foreach ($xml->Video->Role as $Roles) {
						$actors[] = $Roles['tag'];
						if (++$roleCount == 5) {
							break;
						}
					}
					echo implode('</li><li>', $actors);
				} else {
					echo 'n/a';
				}
			echo '</li></ul></div>';
		echo '</div>'; // .summary-content-people-wrapper
	echo '</div>'; // .span3
}

function printShowWatched($xml) {
	global $database;
	$plexWatchDbTable = dbTable();
	echo '<div class="container-fluid">';
		echo '<div class="clear"></div>';
		echo '<div class="row-fluid">';
			echo '<div class="span12">';
				echo '<div class="wellbg">';
					echo '<div class="wellheader">';
					echo'<h3>The most watched episodes of <strong>' .
						$xml->Directory['title'] . '</strong> are</h3>';
				echo'</div>';
				echo '<div class="info-top-watched-wrapper">';
					echo '<ul class="info-top-watched-instance">';
						// Run through each feed item
						$query = 'SELECT title,time,user,orig_title,orig_title_ep,episode,' .
								'season,xml,datetime(time, \'unixepoch\') AS time, ' .
								'COUNT(*) AS play_count ' .
							'FROM ' . $plexWatchDbTable . ' ' .
							'WHERE orig_title LIKE "' . $xml->Directory['title'] . '" ' .
							'GROUP BY title ' .
							'HAVING play_count > 0 ' .
							'ORDER BY play_count DESC,time DESC ' .
							'LIMIT 7';
						$results = getResults($database, $query);
						$numRows = 0;
						while ($row = $results->fetch(PDO::FETCH_ASSOC)) {
							$numRows++;
							$rawShowXML = $row['xml'];
							$showXML = simplexml_load_string($rawShowXML);
							$thumb = $showXML['thumb'] . '&width=205&height=115';
							echo '<li>';
								echo '<div class="info-top-watched-instance-position-circle">';
									echo '<h1>' . $numRows . '</h1>';
								echo '</div>';
								echo '<div class="info-top-watched-poster">';
									echo '<div class="info-top-watched-poster-face">';
										echo '<a href="info.php?id=' . $showXML['ratingKey'] . '">';
											echo '<img src="includes/img.php?img=' .
												urlencode($thumb) .
												'" class="info-top-watched-poster-face">';
										echo '</a>';
									echo '</div>';
									echo '<div class="info-top-watch-card-overlay">';
										echo '<div class="info-top-watched-season">';
											echo 'Season ' . $row['season'] . ',' .
												' Episode ' . $row['episode'];
										echo '</div>';
										echo '<div class="info-top-watched-playcount">';
											echo '<strong>' . $row['play_count'] .
												'</strong> views';
										echo '</div>';
									echo '</div>';
								echo '</div>';
								echo '<div class="info-top-watched-instance-text-wrapper">';
									echo '<div class="info-top-watched-title">';
										echo '<a href="info.php?id=' . $showXML['ratingKey'] .
											'"> "'.$row['orig_title_ep'] . '"</a>';
									echo '</div>';
								echo '</div>';
							echo '</li>';
						}
					echo '</ul>'; // .info-top-watched-instance
				echo '</div>'; // .info-top-watched-wrapper
			echo '</div>'; // .span12
		echo '</div>'; // .row-fluid
	echo '</div>'; // .container-fluid
}

function printSeasonEpisodes($xml) {
	global $itemId;
	echo '<div class="container-fluid">';
		echo '<div class="clear"></div>';
		echo '<div class="row-fluid">';
			echo '<div class="span12">';
				echo '<div class="wellbg">';
					echo '<div class="wellheader">';
						echo '<div class="dashboard-wellheader">';
							echo'<h3>' . $xml->Directory['title'] . '</h3>';
						echo '</div>';
					echo '</div>';
					$seasonEpisodesPath = '/library/metadata/' . $itemId . '/children';
					$seasonEpisodesXml = simplexml_load_string(getPmsData($seasonEpisodesPath));
					if ($seasonEpisodesXml === false) {
						$error_msg = 'Feed Not Found';
						echo '<p>' . $error_msg . '</p>';
						trigger_error($error_msg, E_USER_ERROR);
					}
					echo '<div class="season-episodes-wrapper">';
						echo '<ul class="season-episodes-instance">';
							foreach ($seasonEpisodesXml->Video as $seasonEpisode) {
								$thumbUrl = $seasonEpisode['thumb'] . '&width=205&height=115';
								echo '<li>';
									echo '<div class="season-episodes-poster">';
										echo '<div class="season-episodes-poster-face">';
											echo '<a href="info.php?id=' . $seasonEpisode['ratingKey'] . '">';
												echo '<img src="includes/img.php?img=' . urlencode($thumbUrl) .
													'" class="season-episodes-poster-face">';
												echo '';
											echo '</a>';
										echo '</div>';
										echo '<div class="season-episodes-card-overlay">';
											echo '<div class="season-episodes-season">';
												echo 'Episode ' . $seasonEpisode['index'];
											echo '</div>';
										echo '</div>';
									echo '</div>';
									echo '<div class="season-episodes-instance-text-wrapper">';
										echo '<div class="season-episodes-title">';
											echo '<a href="info.php?id=' . $seasonEpisode['ratingKey'] . '">';
												echo '"' . $seasonEpisode['title'] . '"';
											echo '</a>';
										echo '</div>';
									echo '</div>';
								echo '</li>';
							}
						echo '</ul>';
					echo '</div>';  // .season-episodes-wrapper
				echo '</div>'; // .wellbg
			echo '</div>'; // .span12
		echo '</div>'; // .row-fluid
	echo '</div>'; // .container-fluid
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>plexWatch</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- css styles -->
		<link href="css/plexwatch.css" rel="stylesheet">
		<link href="css/font-awesome.css" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<link href="css/plexwatch-tables.css" rel="stylesheet">
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 40px;
			}
			.sidebar-nav {
				padding: 9px 0;
			}
		</style>

		<!-- touch icons -->
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="apple-touch-icon" href="images/icon_iphone.png">
		<link rel="apple-touch-icon" sizes="72x72" href="images/icon_ipad.png">
		<link rel="apple-touch-icon" sizes="114x114" href="images/icon_iphone@2x.png">
		<link rel="apple-touch-icon" sizes="144x144" href="images/icon_ipad@2x.png">
	</head>
	<body>
		<div class="container">
			<div class="navbar navbar-fixed-top">
				<div class="navbar-inner">
					<a href="index.php"><div class="logo hidden-phone"></div></a>
					<ul class="nav">
						<li><a href="index.php"><i class="fa fa-home fa-2x" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
						<li><a href="history.php"><i class="fa fa-history fa-2x" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
						<li><a href="users.php"><i class="fa fa-users fa-2x" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
						<li><a href="stats.php"><i class="fa fa-area-chart fa-2x" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
						<li><a href="charts.php"><i class="fa fa-bar-chart fa-2x" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
						<li><a href="settings.php"><i class="fa fa-cogs fa-2x" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<?php
		$msg = '<div class="container-fluid">' .
			'<div class="row-fluid">' .
				'<div class="span10 offset1">' .
					'<h3>This media is no longer available in the Plex Media Server database.</h3>' .
				'</div></div></div>';
		$xml = simplexml_load_string(getPmsData($infoPath)) or die ($msg);
		if ($xml->Video['type'] == 'episode') {
			printMetadata($xml);
			printHistory($xml);
		} else if ($xml->Directory['type'] == 'show') {
			printMetadata($xml);
			printShowWatched($xml);
		} else if ($xml->Directory['type'] == 'season') {
			printMetadata($xml);
			printSeasonEpisodes($xml);
		} else if ($xml->Video['type'] == 'movie') {
			printMetadata($xml);
			printHistory($xml);
		}
		?>
		<footer>
		</footer>
		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
		<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
		<script src="js/jquery.rateit.js"></script>
		<script src="js/moment-with-locale.js"></script>

		<script>
			var p = $('.summary-content-summary p');
			var divh = $('.summary-content-summary').height();
			function replaceText(index, text) {
				return text.replace(/\W*\s(\S)*$/, '...');
			}
			while ($(p).outerHeight() > divh) {
				$(p).text(replaceText);
			}
		</script>
		<script>
			$(document).ready(function() {
				var oTable = $('#globalHistory').dataTable({
					"bPaginate": true,
					"bLengthChange": true,
					"bFilter": true,
					"bSort": true,
					"bInfo": true,
					"bAutoWidth": true,
					"aaSorting": [[ 0, "desc" ]],
					"bStateSave": false,
					"bSortClasses": false,
					"sPaginationType": "bootstrap",
					"aoColumns": [
						{
							"mData": function ( source, type, val ) {
								if (type === 'set') {
									source.date = val;
									// Store the computed dislay and filter values for efficiency
									source.date_display = val === "" ? "" : moment(val,"X").format("<?php echo $plexWatch['dateFormat'];?>");
									source.date_filter = val === "" ? "" : val;
									return;
								} else if (type === 'display') {
									return source.date_display;
								} else if (type === 'filter') {
									return source.date_filter;
								}
								// 'sort', 'type' and undefined all just use the integer
								return source.date;
							}
						},
						null,
						null,
						null,
						{
							"bUseRendered": false,
							"mRender": function ( data, type, row ) {
								return moment(data,"X").format("<?php echo $plexWatch['timeFormat'];?>");
							}
						},
						null,
						{
							"bUseRendered": false,
							"mRender": function ( data, type, row ) {
								return moment(data,"X").format("<?php echo $plexWatch['timeFormat'];?>");
							}
						},
						null,
						null
					]
				});
			});
		</script>
		<script>
			$(document).ready(function() {
				$('#home').tooltip();
			});
			$(document).ready(function() {
				$('#history').tooltip();
			});
			$(document).ready(function() {
				$('#users').tooltip();
			});
			$(document).ready(function() {
				$('#charts').tooltip();
			});
			$(document).ready(function() {
				$('#settings').tooltip();
			});
			$(document).ready(function() {
				$('#stats').tooltip();
			});
		</script>
	</body>
</html>
