<?php require_once('serverdatapdo.php'); ?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>plexWatch</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="">
		<meta name="author" content="">

		<!-- Le styles -->
		<link href="css/plexwatch.css" rel="stylesheet">
		<link href="css/font-awesome.css" rel="stylesheet" >
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
		<link href="css/plexwatch-tables.css" rel="stylesheet">
		<link href="css/xcharts.css" rel="stylesheet" >
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
		</style>

		<!-- touch icons -->
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="apple-touch-icon" href="images/icon_iphone.png">
		<link rel="apple-touch-icon" sizes="72x72" href="images/icon_ipad.png">
		<link rel="apple-touch-icon" sizes="114x114" href="images/icon_iphone@2x.png">
		<link rel="apple-touch-icon" sizes="144x144" href="images/icon_ipad@2x.png">
	</head>
	<body>

	<?php $page = 'history'; include 'header.php' ?>

		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12">
					<div class="wellheader-bg">
						<div class="dashboard-wellheader-no-chevron">
							<h2><i class="fa fa-history"></i> History</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span12">
					<div class="wellbg">
						<?php
						// Now generate the HTML databable structure from SQL here:
						//Column names for datatable headings (typically same as sql)
						$cols = "id,Date,User,Platform,IP Address,Title,Started,Paused," .
							"Stopped,xml,Duration,Completed";
						$html = ServerDataPDO::build_html_datatable($cols, 'history_datatable');
						echo $html;
						?>
						<div id="info-modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="info-modal" aria-hidden="true">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-remove"></i></button>
								<h3 id="myModalLabel">Stream Info: <strong><span id="modal-stream-info"></span></strong></h3>
							</div>
							<div class="modal-body" id="modal-text">
							</div>
							<div class="modal-footer">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script src="js/jquery-2.0.3.js"></script>
		<script src="js/bootstrap.js"></script>
		<script src="js/jquery.dataTables.js"></script>
		<script src="js/jquery.dataTables.plugin.bootstrap_pagination.js"></script>
		<script src="js/d3.v3.js"></script>
		<script src="js/moment-with-locale.js"></script>
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
		<?php
		$plexWatchDbTable = dbTable();
		$db_array = array(
			'sql'=>"SELECT id|time|user|platform|ip_address|title|time|paused_counter|stopped|xml|round((julianday(datetime(stopped,'unixepoch', 'localtime')) - julianday(datetime(time,'unixepoch', 'localtime')))*86400)-(case when paused_counter is null then 0 else paused_counter end) from ".$plexWatchDbTable, /* Use | as delimiter. Spell out columns names no SELECT * Table */
			'table'=>$plexWatchDbTable, /* DB table to use assigned by constructor*/
			'idxcol'=>'id' /* Indexed column (used for fast and accurate table cardinality) */
		);
		$javascript = ServerDataPDO::build_jquery_datatable($db_array, 'history_datatable', 'datafactory/get-info-modal.php');

		echo $javascript;
		?>
	</body>
</html>