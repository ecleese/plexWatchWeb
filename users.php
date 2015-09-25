<?php
require_once(dirname(__FILE__) . '/includes/functions.php');
require_once(dirname(__FILE__) . '/includes/timeago.php');
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
		<link href="css/font-awesome.css" rel="stylesheet" >
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

	<?php $page = 'users'; include 'header.php' ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12">
					<div class="wellheader-bg">
						<div class="dashboard-wellheader-no-chevron">
							<h2><i class="fa fa-group"></i> Users</h2>
						</div>
					</div>
					<?php
					$database = dbconnect();
					$plexWatchDbTable = dbTable('user');

					$query = "SELECT COUNT(title) as plays, user, time, platform, " .
							"ip_address, xml " .
						"FROM $plexWatchDbTable " .
						"GROUP BY user " .
						"ORDER BY user " .
						"COLLATE NOCASE";
					$users = getResults($database, $query);
					echo '<div class="wellbg">';
						echo '<table id="usersTable" class="display">';
							echo '<thead>';
								echo '<tr>';
									echo '<th align="right"></th>';
									echo '<th align="left"><i class="fa fa-sort"></i> User </th>';
									echo '<th align="left"><i class="fa fa-sort"></i> Last Seen </th>';
									echo '<th>Hidden sort time</th>';
									echo '<th align="left"><i class="fa fa-sort"></i> Last Known IP </th>';
									echo '<th align="left"><i class="fa fa-sort"></i> Total Plays</th>';
								echo '</tr>';
							echo '</thead>';
							echo '<tbody>';
								// Run through each feed item
								while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
									$userXml = simplexml_load_string($user['xml']) ;
									echo '<tr>';
										echo '<td align="right" width="40px">';
											echo '<div class="users-poster-face">';
												echo '<a href="user.php?user=' . $user['user'] . '">';
												if (empty($userXml->User['thumb'])) {
													// Checking for jpg user pictures.
													if (file_exists(dirname(__FILE__).'/images/users/'.$user['user'].'.jpg')) {
														echo '<img src="images/users/'.$user['user'].'.jpg"';
													// Checking for png user pictures.
													} else if (file_exists(dirname(__FILE__).'/images/users/'.$user['user'].'.png')) {
														echo '<img src="images/users/'.$user['user'].'.png"';
													} else {
														echo '<img src="images/gravatar-default-80x80.png" ';
													}
													echo 'alt="User Logo" />';
												} else {
													echo '<img src="' . $userXml->User['thumb'] . '" ' .
														'onerror="this.src=\'images/gravatar-default-80x80.png\'"' .
														'alt="User Logo" />';
												}
												echo '</a>';
											echo '</div>';
										echo '</td>';
										echo '<td>';
											echo '<div class="users-name">';
												echo '<a href="user.php?user=' . $user['user'] . '">';
													echo FriendlyName($user['user'], $user['platform']);
												echo '</a>';
											echo '</div>';
										echo '</td>';
										$lastSeenTime = $user['time'];
										echo '<td>' . TimeAgo($lastSeenTime) . '</td>';
										echo '<td>' . $lastSeenTime . '</td>';
										echo '<td>' . $user['ip_address'] . '</td>';
										echo '<td>' . $user['plays'] . '</td>';
									echo '</tr>';
								}
							echo '</tbody>';
						echo '</table>';
					echo '</div>';
					?>
				</div>
			</div><!--/.fluid-row-->
			<footer></footer>
		</div><!--/.fluid-container-->

		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
		<script>
			$(document).ready(function() {
				var oTable = $('#usersTable').dataTable({
					"bPaginate": false,
					"bLengthChange": true,
					"bFilter": false,
					"bSort": true,
					"bInfo": true,
					"bAutoWidth": true,
					"aaSorting": [[ 0, "asc" ]],
					"aoColumnDefs": [{ "iDataSort": 3, "aTargets": [2] }],
					"aoColumns": [{}, {}, {}, { "bVisible": false }, {}, {}],
					"bStateSave": false,
					"bSortClasses": true,
					"sPaginationType": "bootstrap"
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