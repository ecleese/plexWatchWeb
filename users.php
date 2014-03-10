<?php include("../login/include/session.php"); ?>
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
	<link href="css/plexwatch-tables.css" rel="stylesheet">
	<link href="css/font-awesome.min.css" rel="stylesheet" >
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
<?php if($session->logged_in){ ?>
  
  
	<div class="container">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<a href="index.php"><div class="logo hidden-phone"></div></a>
				<ul class="nav">
					
					<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
					<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
					<li class="active"><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
					<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
					<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					
				</ul>
				
			</div>
		</div>
    </div>
	
	<div class="clear"></div>
		
	
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
				<div class='wellheader'>
					<div class="dashboard-wellheader-no-chevron">
						<h2><i class="icon-large icon-group icon-white"></i> Users</h2>
					</div>
				</div>

				<?php
						$guisettingsFile = "config/config.php";
						
						if (file_exists($guisettingsFile)) { 
							require_once(dirname(__FILE__) . '/config/config.php');
						}else{
							header("Location: settings.php");
						}
							
						if ($plexWatch['https'] == 'yes') {
							$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
						}else if ($plexWatch['https'] == 'no') {
							$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
						}	
						
						if (!empty($plexWatch['myPlexAuthToken'])) {
								$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
								
							}else{
								$myPlexAuthToken = '';
								
						}
						
							
						date_default_timezone_set(@date_default_timezone_get());

						$db = dbconnect();
						
						if ($plexWatch['userHistoryGrouping'] == "yes") {
							$plexWatchDbTable = "grouped";
						}else if ($plexWatch['userHistoryGrouping'] == "no") {
							$plexWatchDbTable = "processed";
						}
						
						$users = $db->query("SELECT COUNT(title) as plays, user, time, SUM(time) as timeTotal, SUM(stopped) as stoppedTotal, SUM(paused_counter) as paused_counterTotal, platform, ip_address, xml FROM ".$plexWatchDbTable." GROUP BY user ORDER BY user COLLATE NOCASE") or die ("Failed to access plexWatch database. Please check your settings.");
					
							
							echo "<div class='wellbg'>";
							
							echo "<table id='usersTable' class='display'>";
								echo "<thead>";
									echo "<tr>";
										echo "<th align='right'></th>";
										echo "<th align='left'>User </th>";
										echo "<th align='left'>Last Seen </th>";
										echo "<th align='left'>Last Known IP </th>";
										echo "<th align='left'>Total Plays</th>";
										
									echo "</tr>";
								echo "</thead>";
								echo "<tbody>";
							
								// Run through each feed item
								while ($user = $users->fetchArray()) {
							
									$userXml = simplexml_load_string($user['xml']) ;                         

										echo "<tr>";
											echo "<td align='right' width='40px'>";
												if (empty($userXml->User['thumb'])) {				
													echo "<div class='users-poster-face'><a href='user.php?user=".$user['user']."'><img src='images/gravatar-default-80x80.png' /></a></div>";			
												}else{
													echo "<div class='users-poster-face'><a href='user.php?user=".$user['user']."'><img src='".$userXml->User['thumb']."' onerror=\"this.src='images/gravatar-default-80x80.png'\" /></a></div>";
												}
											echo "</td>";
											echo "<td>";
												echo "<div class='users-name'><a href='user.php?user=".$user['user']."'> ".FriendlyName($user['user'],$user['platform'])."</a> </div>";
											echo "</td>";
														
											require_once(dirname(__FILE__) . '/includes/timeago.php');
											$lastSeenTime = $user['time'];

											echo "<td>".TimeAgo($lastSeenTime)."</td>";
														
											echo "<td>".$user['ip_address']."</td>";
													
											echo "<td>".$user['plays']."</td>";
					
											
																		
										echo "</tr>";   
												
								}
								
								echo "</tbody>";
								echo "</table>";
						
					
					?>
						</div>
				
			</div>
		</div><!--/.fluid-row-->			
			
			

		<footer>
		
		</footer>
		
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
			var oTable = $('#usersTable').dataTable( {
				"bPaginate": false,
				"bLengthChange": true,
				"bFilter": false,
				"bSort": false,
				"bInfo": true,
				"bAutoWidth": true,
				"aaSorting": [[ 0, "asc" ]],
				"bStateSave": false,
				"bSortClasses": true,
				"sPaginationType": "bootstrap"	
			} );
		} );
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
<?php } ?>
  </body>
</html>
