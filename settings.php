<?php
$guisettingsFile = dirname(__FILE__) . '/config/config.php';
if (file_exists($guisettingsFile)) {
	require_once($guisettingsFile);
}

function printSettings() {
	global $guisettingsFile;
	$haveConfig = file_exists($guisettingsFile);
	echo '<div class="row-fluid">';
		echo '<div class="span3">';
			echo '<ul class="nav nav-list">';
				echo '<li class="active"><a href="#info">General</a></li>';
				echo '<li><a href="#pms">PMS &amp; Database</a></li>';
				echo '<li><a href="#myplex">Plex.tv Authentication</a></li>';
				echo '<li><a href="#grouping">Grouping</a></li>';
			echo '</ul>';
		echo '</div>';
		echo '<div class="span9">';
			echo '<form action="includes/process_settings.php" method="POST">';
				echo '<fieldset>';
					if ($haveConfig) {
						printVersions();
					}
					printGeneralSettings($haveConfig);
					printDateTimeFormat();
					printPMSSettings($haveConfig);
					printPlexAuthSettings($haveConfig);
					printGroupingSettings($haveConfig);
					echo '<div class="form-actions">';
						echo '<div class="control-group">';
							echo '<label class="control-label" for="submit"></label>';
							echo '<div class="controls">';
								echo '<div id="friendlyName">';
									echo '<button id="submit" name="submit" ' .
										'class="btn btn-medium btn-primary" value="save">';
										echo 'Save';
									echo '</button>';
									echo '<a href="index.php" class="btn btn-medium btn-cancel">';
										echo 'Cancel';
									echo '</a>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</fieldset>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
}

function printVersions() {
	$database = dbconnect();
	echo '<div class="wellbg">';
		echo '<div class="wellheader">';
			echo '<div class="dashboard-wellheader">';
				echo '<h3><a id="version">Version Information</a></h3>';
			echo '</div>';
		echo '</div>';
		echo '<div class="settings-general-info">';
			echo '<ul>';
				echo '<li>plexWatch/Web Version: <strong>v1.7.0 dev</strong></li>';
				$query = "SELECT version FROM config";
				$plexWatchVersion = $database->querySingle($query);
				echo '<li>plexWatch Version: <strong>';
					echo 'v' . $plexWatchVersion;
				echo '</strong></li>';
			echo '</ul>';
		echo '</div>';
	echo '</div>';
}

function printGeneralSettings($haveConfig) {
	global $plexWatch;
	echo '<div class="wellbg">';
		echo '<div class="wellheader">';
			echo '<div class="dashboard-wellheader">';
				echo '<h3><a id="info">General</a></h3>';
			echo '</div>';
		echo '</div>';
		// Text input
		echo '<div class="control-group">';
			echo '<p class="help-block">';
				echo 'The date &amp; display format plexWatch/Web should use. ';
				echo '<a href="#dateTimeOptionsModal" data-toggle="modal">';
					echo 'Format options';
				echo '</a>';
			echo '</p>';
			echo '<br>';
			echo '<label class="control-label" for="dateFormat">Date Format</label>';
			echo '<div class="controls">';
				echo '<input id="dateFormat" name="dateFormat" type="text" ' .
					'placeholder="M/D/YYYY" class="input-small" required="" ' .
					'value="';
					if ($haveConfig) {
						echo $plexWatch['dateFormat'];
					} else {
						echo 'M/D/YYYY';
					}
					echo '">';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="timeFormat">Time Format</label>';
			echo '<div class="controls">';
				echo '<input id="timeFormat" name="timeFormat" type="text" '.
					'placeholder="hh:mm a" class="input-mini" required="" value="';
					if ($haveConfig) {
						echo $plexWatch['timeFormat'];
					} else {
						echo 'hh:mm a';
					}
					echo '">';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

function printDateTimeFormat() {
	echo '<div id="dateTimeOptionsModal" class="modal hide fade" tabindex="-1" ' .
		'role="dialog" aria-labelledby="dateTimeOptionsModal" aria-hidden="true">';
		echo '<div class="modal-header">';
			echo '<button type="button" class="close" data-dismiss="modal" ' .
				'aria-hidden="true">';
				echo '<i class="icon icon-remove"></i>';
			echo '</button>';
			echo '<h3 id="myModalLabel">Date &amp; Time Format Options</h3>';
		echo '</div>';
		echo '<div class="modal-body">';
			echo '<div class="span12">';
				echo '<table>';
					echo '<tbody>';
						printDTRows();
					echo '</tbody>';
				echo '</table>';
			echo '</div>';
		echo '</div>';
		echo '<div class="modal-footer"></div>';
	echo '</div>';
}

function printDTRows() {
	echo '<tr><td colspan="3"><h5>Day</h5></td></tr>';
	echo '<tr>';
		echo '<td align="center"><strong>DD</strong></td>';
		echo '<td width="300">Numeric, with leading zeros</td>';
		echo '<td>01 to 31</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>D</strong></td>';
		echo '<td>Numeric, without leading zeros</td>';
		echo '<td>1 to 31</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>Do</strong></td>';
		echo '<td>The English suffix for the day of the month</td>';
		echo '<td>st, nd or th in the 1st, 2nd or 15th.</td>';
	echo '</tr>';

	echo '<tr><td colspan="3"><h5>Month</h5></td></tr>';
	echo '<tr>';
		echo '<td align="center"><strong>MM</strong></td>';
		echo '<td>Numeric, with leading zeros</td>';
		echo '<td>01 to 31</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>M</strong></td>';
		echo '<td>Numeric, without leading zeros</td>';
		echo '<td>1 to 31</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>MMMM</strong></td>';
		echo '<td>Textual full</td>';
		echo '<td>January – December</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>MMM</strong></td>';
		echo '<td>Textual three letters</td>';
		echo '<td>Jan – Dec</td>';
	echo '</tr>';

	echo '<tr><td colspan="3"><h5>Year</h5></td></tr>';
	echo '<tr>';
		echo '<td align="center"><strong>YYYY</strong></td>';
		echo '<td>Numeric, 4 digits</td>';
		echo '<td>Eg., 1999, 2003</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>YY</strong></td>';
		echo '<td>Numeric, 2 digits</td>';
		echo '<td>Eg., 99, 03</td>';
	echo '</tr>';

	echo '<tr><td colspan="3"><h5>Time</h5></td></tr>';
	echo '<tr>';
		echo '<td align="center"><strong>a</strong></td>';
		echo '<td width="300">am/pm Lowercase</td>';
		echo '<td>am, pm</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>A</strong></td>';
		echo '<td>AM/PM Uppercase</td>';
		echo '<td>AM, PM</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>h</strong></td>';
		echo '<td>Hour, 12-hour, without leading zeros</td>';
		echo '<td>1–12</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>hh</strong></td>';
		echo '<td>Hour, 12-hour, with leading zeros</td>';
		echo '<td>01–12</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>H</strong></td>';
		echo '<td>Hour, 24-hour, without leading zeros</td>';
		echo '<td>0-23</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>HH</strong></td>';
		echo '<td>Hour, 24-hour, with leading zeros</td>';
		echo '<td>00-23</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>mm</strong></td>';
		echo '<td>Minutes, with leading zeros</td>';
		echo '<td>00-59</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>ss</strong></td>';
		echo '<td>Seconds, with leading zeros</td>';
		echo '<td>00-59</td>';
	echo '</tr>';
	echo '<tr>';
		echo '<td align="center"><strong>zz</strong></td>';
		echo '<td>Timezone abbreviation</td>';
		echo '<td>Eg., EST, MDT ...</td>';
	echo '</tr>';
}

function printPMSSettings($haveConfig) {
	global $plexWatch;
	echo '<div class="wellbg">';
		echo '<div class="wellheader">';
			echo '<div class="dashboard-wellheader">';
				echo '<h3><a id="pms">Plex Media Server &amp; Database Settings</a></h3>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="pmsIp">PMS IP Address</label>';
			echo '<div class="controls">';
				echo '<input id="pmsIp" name="pmsIp" type="text" placeholder="0.0.0.0" ' .
					'class="input-xlarge" required=""';
					if ($haveConfig) {
						echo ' value="' . $plexWatch['pmsIp'] . '"';
					}
					echo '>';
				echo '<p class="help-block">';
					echo 'Plex Media Server IP address, hostname or domain name';
				echo '</p>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="pmsHttpPort">PMS Web Port</label>';
			echo '<div class="controls">';
				echo '<input id="pmsHttpPort" name="pmsHttpPort" type="text" ' .
					'placeholder="32400" class="input-mini" required="" value="';
					if ($haveConfig) {
						echo $plexWatch['pmsHttpPort'];
					} else {
						echo '32400';
					}
					echo '">';
				echo '<p class="help-block">Plex Media Server\'s web port</p>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="plexWatchDb">plexWatch Database</label>';
			echo '<div class="controls">';
				echo '<input id="plexWatchDb" name="plexWatchDb" type="text" '.
				'placeholder="/opt/plexWatch/plexWatch.db" class="input-xlarge" ' .
				'required="" value="';
					if ($haveConfig) {
						echo $plexWatch['plexWatchDb'];
					} else {
						echo '/opt/plexWatch/plexWatch.db';
					}
					echo '">';
				echo '<p class="help-block">File location of your plexWatch database.</p>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

function printPlexAuthSettings($haveConfig) {
	global $plexWatch;
	echo '<div class="wellbg">';
		echo '<div class="wellheader">';
			echo '<div class="dashboard-wellheader">';
				echo '<h3><a id="myplex">Plex.tv Authentication</a></h3>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<p class="help-block">';
				echo 'If you have enabled ';
				echo '<a href="https://support.plex.tv/hc/en-us/articles/203815766-What-is-Plex-Home-">';
				echo 'Plex Home</a> on your Plex Media Server, a ';
				echo '<a href="https://plex.tv/users/sign_in">Plex.tv</a> username and ';
				echo 'password are required in order to access your server\'s data.';
			echo '</p>';
			echo '<br>';
			echo '<label class="control-label" for="myPlexUser">Username (optional)</label>';
			echo '<div class="controls">';
				echo '<input id="myPlexUser" name="myPlexUser" type="text"';
					'placeholder="" class="input-xlarge" ';
					if ($haveConfig) {
						echo ' value="' .$plexWatch['myPlexUser']. '"';
					}
					echo '>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="myPlexPass">Password (optional)</label>';
			echo '<div class="controls">';
				echo '<input id="myPlexPass" name="myPlexPass" type="password" ' .
					'placeholder="" class="input-xlarge" value="">';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

function printGroupingSettings($haveConfig) {
	global $plexWatch;
	$globalGrouping = '';
	$userGrouping = '';
	$chartsGrouping = '';
	if ($haveConfig) {
		if ($plexWatch['globalHistoryGrouping'] == 'yes') {
			$globalGrouping = ' checked';
		}
		if ($plexWatch['userHistoryGrouping'] == 'yes') {
			$userGrouping = ' checked';
		}
		if ($plexWatch['chartsGrouping'] == 'yes') {
			$chartsGrouping = ' checked';
		}
	}
	echo '<div class="wellbg">';
		echo '<div class="wellheader">';
			echo '<div class="dashboard-wellheader">';
				echo '<h3><a id="grouping">Grouping Settings</a></h3>';
			echo '</div>';
		echo '</div>';
		echo '<div class="control-group">';
			echo '<label class="control-label" for="globalHistoryGrouping-0">' .
				'Global History (optional)</label>';
			echo '<div class="controls">';
				echo '<label class="checkbox inline" for="globalHistoryGrouping-0">';
					echo '<input type="checkbox" name="globalHistoryGrouping" ' .
						'id="globalHistoryGrouping-0" value="yes"' . $globalGrouping .'>';
					echo '<span class="help-block">Enable global history grouping</span>';
				echo '</label>';
				echo '<label class="control-label" for="userHistoryGrouping-0">' .
					'User History (optional)</label>';
				echo '<label class="checkbox inline" for="userHistoryGrouping-0">';
					echo '<input type="checkbox" name="userHistoryGrouping" ' .
						'id="userHistoryGrouping-0" value="yes"' .$userGrouping .'>';
					echo '<span class="help-block">Enable user history grouping</span>';
				echo '</label>';
				echo '<label class="control-label" for="chartsGrouping-0">';
					echo 'Charts (optional)';
				echo '</label>';
				echo '<label class="checkbox inline" for="chartsGrouping-0">';
					echo '<input type="checkbox" name="chartsGrouping" ' .
						'id="chartsGrouping-0" value="yes"' .$chartsGrouping. '>';
					echo '<span class="help-block">Enable charts grouping</span>';
				echo '</label>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
}

function printWelcomeModal() {
	echo '<div id="welcomeModal" class="modal hide fade" tabindex="-1" '.
		'role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
		echo '<div class="modal-header">';
			echo '<h2 id="myModalLabel">';
				echo '<i class="icon-large icon-chevron-right icon-white"></i> ';
				echo 'Get Started';
			echo '</h2>';
		echo '</div>';
		echo '<div class="modal-body">';
			echo '<img src="images/logo-plexWatch-welcome.png">';
			echo '<h1>Welcome to plexWatch/Web</h1>';
			echo '<p>PlexWatch/Web makes it easy to view in-depth historical ' .
				'statistics and activity of your Plex Media Server. Let\'s get ' .
				'started by checking for some requirements.</p>';
			printServerSupport();
			printPHPSupport();
			printSQLiteSupport();
			printCURLSupport();
			printJSONSupport();
			echo '<li>';
				echo '<i class="icon icon-ok"></i> ';
				echo 'Your server"s timezone: <strong>';
					echo '<span class="label label-warning">';
						echo @date_default_timezone_get();
					echo '</span>';
				echo '</strong>';
			echo '</li>';
			echo '<br>';
			echo '<p><h4>Note: </h4>Please ensure you have installed, configured ' .
				'and tested <a href="https://github.com/ljunkie/plexWatch">plexWatch ' .
				'v0.3.2</a> or above before continuing. If all requirements above are ' .
				'green and the timezone shown matches your timezone you can move '.
				'forward by filling in a few key configuration options now.</p>';
			echo '<br>';
		echo '</div>';
		echo '<div class="modal-footer">';
			echo '<button class="btn btn-primary pull-right" data-dismiss="modal" ' .
				'aria-hidden="true">I\'m ready to go.</button>';
		echo '</div>';
	echo '</div>';
}

function printServerSupport() {
	echo '<li>';
		if (isset($_SERVER['SERVER_SOFTWARE'])) {
				echo '<i class="icon icon-ok"></i> ';
				echo 'Web Server: <strong>';
					echo '<span class="label label-success">';
						echo $_SERVER['SERVER_SOFTWARE'];
					echo '</span>';
				echo '</strong>';
		} else {
				echo '<i class="icon icon-warning-sign"></i> ';
				echo 'Web Server: <strong>';
					echo '<span class="label label-important">';
						echo 'No information available';
					echo '</span>';
				echo '</strong>';
		}
	echo '</li>';
}

function printPHPSupport() {
	echo '<li>';
		$phpVersion = phpversion();
		if (!empty($phpVersion)) {
				echo '<i class="icon icon-ok"></i> ';
				echo 'PHP Version: <strong>';
					echo '<span class="label label-success">';
						echo 'v' . $phpVersion;
					echo '</span>';
				echo '</strong>';
		} else {
			echo '<i class="icon icon-warning-sign"></i> ';
			echo 'PHP Version: <strong>';
				echo '<span class="label label-important">';
					echo 'No information available';
				echo '</span>';
			echo '</strong>';
		}
	echo '</li>';
}

function printSQLiteSupport() {
	echo '<li>';
		$sqliteVersion = SQLite3::version();
		if (!empty($sqliteVersion)) {
			echo '<i class="icon icon-ok"></i> ';
			echo 'PHP SQLite Support: <strong>';
				echo '<span class="label label-success">';
					echo 'v' . $sqliteVersion['versionString'];
				echo '</span>';
			echo '</strong>';
		} else {
			echo '<i class="icon icon-warning-sign"></i> ';
			echo 'PHP SQLite Support: <strong>';
				echo '<span class="label label-important">';
					echo 'No information available';
				echo '</span>';
			echo '</strong>';
		}
	echo '</li>';
}

function printCURLSupport() {
	echo '<li>';
		$curlVersion = curl_version();
		echo '<i class="icon icon-ok"></i> ';
		echo 'PHP Curl Support: <strong>';
			echo '<span class="label label-success">';
				echo $curlVersion['version'];
			echo '</span>';
		echo '</strong>';
		echo ' / SSL Support: <strong>';
			echo '<span class="label label-success">';
				echo $curlVersion['ssl_version'];
			echo '</span>';
		echo '</strong>';
	echo '</li>';
}

function printJSONSupport() {
	echo '<li>';
		echo '<i class="icon icon-ok"></i> ';
		echo 'PHP JSON Support: <strong>';
		$json[] = '{"Yes":""}';
		foreach ($json as $string) {
			json_decode($string);
			switch (json_last_error()) {
				case JSON_ERROR_NONE:
					echo '<span class="label label-success">';
						echo 'Yes';
					echo '</span>';
					break;
				case JSON_ERROR_DEPTH:
					echo '<span class="label label-important">';
						echo 'Maximum stack depth exceeded';
					echo '</span>';
					break;
				case JSON_ERROR_STATE_MISMATCH:
					echo '<span class="label label-important">';
						echo 'Underflow or the modes mismatch';
					echo '</span>';
					break;
				case JSON_ERROR_CTRL_CHAR:
					echo '<span class="label label-important">';
						echo 'Unexpected control character found';
					echo '</span>';
					break;
				case JSON_ERROR_SYNTAX:
					echo '<span class="label label-important">';
						echo 'Syntax error, malformed JSON';
					echo '</span>';
					break;
				case JSON_ERROR_UTF8:
					echo '<span class="label label-important">';
						echo 'Malformed UTF-8 characters, possibly ' .
							'incorrectly encoded';
					echo '</span>';
					break;
				default:
					echo '<span class="label label-important">';
						echo 'No (Unknown Error)';
					echo '</span>';
					break;
			}
		}
		echo '</strong>';
	echo '</li>';
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

		<!-- css -->
		<link href="css/plexwatch.css" rel="stylesheet">
		<link href="css/font-awesome.min.css" rel="stylesheet">
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
						<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
						<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
						<li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
						<li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
						<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
						<li class="active"><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="container">
			<div class="row">
				<div class="span12">
					<div class="wellheader">
						<div class="dashboard-wellheader-no-chevron">
							<h2><i class="icon-large icon-wrench icon-white"></i> Settings</h2>
						</div>
					</div>
					<div class="wellbg">
						<?php
						if (!class_exists('SQLite3')) {
							$error_msg = 'php5-sqlite is not installed. Please install this ' .
								'requirement and restart your webserver before continuing.';
							echo '<div class="alert alert-warning">' . $error_msg . '</div>';
							trigger_error($error_msg, E_USER_ERROR);
						}
						// Check for a successful form post
						$errorStart = '<div class="alert alert-warning alert-dismissable">';
						$errorEnd = '<button type="button" class="close" ' .
									'data-dismiss="alert" aria-hidden="true"><i class="icon ' .
									'icon-remove-circle"></i></button></div>';
						if (isset($_GET['s'])) {
							echo $errorStart . htmlspecialchars($_GET['s'], ENT_QUOTES | ENT_SUBSTITUTE,
								'UTF-8', true) . $errorEnd;
						} elseif (isset($_GET['e'])) {
							// check for a form error
							echo $errorStart . htmlspecialchars($_GET['e'], ENT_QUOTES | ENT_SUBSTITUTE,
								'UTF-8', true) . $errorEnd;
						} elseif (isset($_GET['error']) && ($_GET['error'] == 'datetime')) {
							echo $errorStart . 'Error in date/time format. Please correct ' .
								'this and try again.' . $errorEnd;
						}
						printSettings();
						if (!file_exists($guisettingsFile) && !isset($_GET['e'])) {
							printWelcomeModal();
						}
						?>
					</div>
				</div>
			</div>
			<footer></footer>
		</div><!--/.container-->

		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
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
		<script>
			$('#welcomeModal').modal('show');
		</script>
		<script>
			$('#dateTimeModal').modal('show');
		</script>
		<script>
			$('#actionSubmit').on('click', function (e) {
				e.preventDefault();
				alert($('#groupedHistory').serialize());
			});

			$('.btn-group').button();
		</script>
		<script>
			window.setTimeout(function() {
				$(".alert-warning").fadeTo(500, 0).slideUp(500, function() {
					$(this).remove();
				});
			}, 5000);
		</script>
	</body>
</html>