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
			.dataTables_processing {
				position: absolute;
				top: 50%;
				left: 50%;
				width: 250px;
				height: 30px;
				margin-left: -125px;
				margin-top: -15px;
				padding: 14px 0 2px 0;
				border: 1px solid #ddd;
				text-align: center;
				color: black;
				font-size: 14px;
				background-color: white;
			}
			.spinner {
				padding-bottom: 25px;
				position: relative;
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
						<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php

		include "serverdatapdo.php";
		$guisettingsFile = "config/config.php";

		if (file_exists($guisettingsFile)) {
			require_once(dirname(__FILE__) . '/config/config.php');
		} else {
			header("Location: settings.php");
		}

		$user = htmlspecialchars($_GET['user'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', true);
		$db = dbconnect();

		if ($plexWatch['userHistoryGrouping'] == "yes") {
			$plexWatchDbTable = "grouped";
		} else if ($plexWatch['userHistoryGrouping'] == "no") {
			$plexWatchDbTable = "processed";
		}

		$userInfo = $db->query("SELECT user,xml FROM ".$plexWatchDbTable." WHERE user = '$user' ORDER BY time DESC LIMIT 1") or die ("Failed to access plexWatch database. Please check your settings.");
		?>
		<div class='container-fluid'>
			<div class='row-fluid'>
				<div class='span12'>
					<div class='user-info-wrapper'>
						<?php
						while ($userInfoResults= $userInfo->fetchArray()) {
							$userInfoXml = $userInfoResults['xml'];
							$userInfoXmlField = simplexml_load_string($userInfoXml);
							if (empty($userInfoXmlField->User['thumb'])) {
								?><div class='user-info-poster-face'><img src='images/gravatar-default-80x80.png'></></div><?php
							} else {
								?><div class='user-info-poster-face'><img src='<?php echo $userInfoXmlField->User['thumb'];?>' onerror=\"this.src='images/gravatar-default-80x80.png'\"></></div><?php
							}
						}
						?>
						<div class='user-info-username'><?php echo FriendlyName($user); ?></div>
						<div class='user-info-nav'>
							<ul class='user-info-nav'>
								<li class='active'><a href='#profile' data-toggle='tab'>Profile</a></li>
								<li><a id='ip-tab-btn' href='#userAddresses' data-toggle='tab'>IP Addresses</a></li>
								<li><a href='#userHistory' data-toggle='tab'>History</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='tab-content'>
			<div class='tab-pane active' id='profile'>
				<div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12'>
							<div class='wellbg'>
								<div class='wellheader'>
									<div class='dashboard-wellheader'>
										<h3>Global Stats</h3>
									</div>
								</div>
								<div id='user-time-stats' class='user-overview-stats-wrapper'><div id='user-stats-spinner' class='spinner'></div></div>
							</div>
						</div>
					</div>
				</div>
				<div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12'>
							<div class='wellbg'>
								<div class='wellheader'>
									<div class='dashboard-wellheader'>
										<h3>Platform Stats</h3>
									</div>
								</div>
								<div id='user-platform-stats' class='user-platforms'><div id='user-platform-spinner' class='spinner'></div></div>
							</div>
						</div>
					</div>
				</div>
				<div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12'>
							<div class='wellbg'>
								<div class='wellheader'>
									<div class='dashboard-wellheader'>
										<h3>Recently watched</h3>
									</div>
								</div>
								<div id='user-recently-watched' class='dashboard-recent-media-row'><div id='user-watched-spinner' class='spinner'></div></div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='tab-pane' id='userAddresses'>
				<div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12'>
							<div class='wellbg'>
								<div class='wellheader'>
									<div class='dashboard-wellheader'>
										<h3>Public IP Addresses for <strong><?php echo $user; ?></strong></h3>
									</div>
								</div>
								<table id='tableUserIpAddresses' class='display' width='100%'>
									<thead>
										<tr>
											<th align='left'><i class='icon-sort icon-white'></i> Last seen</th>
											<th align='left'><i class='icon-sort icon-white'></i> IP Address</th>
											<th align='left'><i class='icon-sort icon-white'></i> Play Count</th>
											<th align='left'><i class='icon-sort icon-white'></i> Platform (Last Seen)</th>
											<th align='left'><i class='icon-sort icon-white'></i> Location</th>
											<th align='left'><i class='icon-sort icon-white'></i> Location</th>
										</tr>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='tab-pane' id='userHistory'>
				<div class='container-fluid'>
					<div class='row-fluid'>
						<div class='span12'>
							<div class='wellbg'>
								<div class='wellheader'>
									<div class='dashboard-wellheader'>
										<h3>Watching History for <strong><?php echo $user; ?></strong></h3>
									</div>
								</div>
								<?php
								//now generate the HTML databable structure from SQL here:
								$cols= "id,Date,User,Platform,IP Address,Title,Started,Paused,Stopped,xml,Duration,Completed";  //Column names for datatable headings (typically same as sql)
								$html = ServerDataPDO::build_html_datatable($cols,'user_history_datatable');
								echo $html;
								?>
								<div id="info-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="info-modal" aria-hidden="true">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>
										<h3 id="myModalLabel"><i class="icon-info-sign icon-white"></i> Stream Info: <strong><span id="modal-stream-info"></span></strong></h3>
									</div>
									<div class="modal-body" id="modal-text"></div>
									<div class="modal-footer"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<footer></footer>

		<!-- javascript
		================================================== -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.dataTables.plugin.date_sorting.js"></script>
		<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
		<script src="js/moment-with-locale.js"></script>
		<script src="js/cacher.js"></script>
		<script src="js/spin.min.js"></script>
		<script>
			function loadXMLString(txt) {
				if (window.DOMParser) {
					parser=new DOMParser();
					xmlDoc=parser.parseFromString(txt,"text/xml");
				} else { // code for IE
					xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
					xmlDoc.async=false;
					xmlDoc.loadXML(txt);
				}
				return xmlDoc;
			}
		</script>
		<script>
			$(document).ready(function() {
				var cacheData = getCache('<?php echo $user;?>'+'-user-recently-watched-cache');
				if (cacheData) {
					$("#user-recently-watched").html(cacheData);
				} else {
					$.ajax({
						url: 'datafactory/get-user-recently-watched.php',
						type: "POST",
						async: true,
						data: { user : '<?php echo $user; ?>' },
						success: function(data) {
							$("#user-recently-watched").html(data);
							setCache('<?php echo $user;?>'+'-user-recently-watched-cache', data);
						}
					});
				}
			});
		</script>
		<script>
			$(document).ready(function() {
				var cacheData = getCache('<?php echo $user;?>'+'-user-time-stats-cache');
				if (cacheData) {
					$("#user-time-stats").html(cacheData);
				} else {
					$.ajax({
						url: 'datafactory/get-user-time-stats.php',
						type: "POST",
						async: true,
						data: { user : '<?php echo $user; ?>' },
						success: function(data) {
							$("#user-time-stats").html(data);
							setCache('<?php echo $user;?>'+'-user-time-stats-cache', data);
						}
					});
				}
			});
		</script>
		<script>
			$(document).ready(function() {
				var cacheData = getCache('<?php echo $user;?>'+'-user-platform-stats-cache');
				if (cacheData) {
					$("#user-platform-stats").html(cacheData);
				} else {
					$.ajax({
						url: 'datafactory/get-user-platform-stats.php',
						type: "POST",
						async: true,
						data: { user : '<?php echo $user; ?>' },
						success: function(data) {
							$("#user-platform-stats").html(data);
							setCache('<?php echo $user;?>'+'-user-platform-stats-cache', data);
						}
					});
				}
			});
		</script>
		<script>
			$(document).ready(function() {
				var ipTableOptions = {
					"bPaginate": true,
					"bDestroy": true,
					"bLengthChange": true,
					"bFilter": true,
					"bSort": true,
					"bInfo": true,
					"iDisplayLength": 10,
					"aaSorting": [[0,'desc']],
					"bAutoWidth": true,
					"bProcessing": true,
					"bStateSave": false,
					"bSortClasses": false,
					"sPaginationType": "bootstrap",
					"aaData": [{
						0: "Loading...",
						1: "",
						2: "",
						3: "",
						4: "",
						5: ""
					}],
					"aoColumnDefs": [
						{
							"aTargets": [ 0 ],
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								if (sData != "Loading...") {
									$(nTd).html(moment(sData,"X").format("<?php echo $plexWatch['dateFormat']; ?>"));
								}
							}
						},
						{
							"aTargets": [ 1 ]
						},
						{
							"aTargets": [ 2 ]
						},
						{
							"aTargets": [ 3 ]
						},
						{
							"aTargets": [ 4 ],
							"fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
								if (oData[5] !== '') {
									$(nTd).html('<a target="_blank" href="'+oData[5]+'"><i class="icon-map-marker icon-white"></i>&nbsp'+sData+'</a>');
								} else {
									$(nTd).html(sData);
								}
							}
						},
						{
							"aTargets": [ 5 ],
							"bVisible": false
						}
					]
				};

				ipTable = $('#tableUserIpAddresses').dataTable(ipTableOptions);

				var cacheData = getCache('<?php echo $user;?>'+'-ip-stats-cache');
				if (cacheData) {
					ipTableOptions.aaData = cacheData.data;
					ipTable = $('#tableUserIpAddresses').dataTable(ipTableOptions);
				} else {
					$.ajax({
						url: "datafactory/get-user-ip-stats.php",
						data: { user: "<?php echo $user; ?>" },
						type: "post",
						dataType: "json",
						async: true,
						success: function(data) {
							ipTableOptions.aaData = data.data;
							ipTable = $('#tableUserIpAddresses').dataTable(ipTableOptions);
							// set expiration on this cached item to 10 minutes.
							setCache('<?php echo $user;?>'+'-ip-stats-cache',data,10);
						}
					} );
				}
			});
		</script>
		<?php
		$plexWatchDbTable = "";
		if ($plexWatch['userHistoryGrouping'] == "yes") {
			$plexWatchDbTable = "grouped";
		} else if ($plexWatch['userHistoryGrouping'] == "no") {
			$plexWatchDbTable = "processed";
		}

		$db_array=array(
			"sql"=>"SELECT id|time|user|platform|ip_address|title|time|paused_counter|stopped|xml|round((julianday(datetime(stopped,'unixepoch', 'localtime')) - julianday(datetime(time,'unixepoch', 'localtime')))*86400)-(case when paused_counter is null then 0 else paused_counter end) from ".$plexWatchDbTable, /* Use | as delimiter. Spell out columns names no SELECT * Table */
			"table"=>$plexWatchDbTable, /* DB table to use assigned by constructor*/
			"idxcol"=>"id", /* Indexed column (used for fast and accurate table cardinality) */
			"where"=>"user = '".$user."'" /* Option where clause (omit WHERE text) */
		);

		$javascript = ServerDataPDO::build_jquery_datatable($db_array,'user_history_datatable','datafactory/get-user-info-modal.php');
		echo $javascript;
		?>
		<script>
			var opts = {
				lines: 8, // The number of lines to draw
				length: 8, // The length of each line
				width: 4, // The line thickness
				radius: 5, // The radius of the inner circle
				corners: 1, // Corner roundness (0..1)
				rotate: 0, // The rotation offset
				direction: 1, // 1: clockwise, -1: counterclockwise
				color: '#fff', // #rgb or #rrggbb or array of colors
				speed: 1, // Rounds per second
				trail: 60, // Afterglow percentage
				shadow: false, // Whether to render a shadow
				hwaccel: false, // Whether to use hardware acceleration
				className: 'spinner', // The CSS class to assign to the spinner
				zIndex: 2e9, // The z-index (defaults to 2000000000)
				top: '0', // Top position relative to parent
				left: '50%' // Left position relative to parent
			};
			var target_a = document.getElementById('user-stats-spinner');
			var spinner_a = new Spinner(opts).spin(target_a);

			var target_b = document.getElementById('user-platform-spinner');
			var spinner_b = new Spinner(opts).spin(target_b);

			var target_c = document.getElementById('user-watched-spinner');
			var spinner_c = new Spinner(opts).spin(target_c);
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