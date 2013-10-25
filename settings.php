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
				<a href="index.php"><div class="logo"></div></a>
				<ul class="nav">
					
					<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
					<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li><a href="users.php"><i class="icon-2x icon-user icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
					<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
					<li class="active"><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
					
				</ul>
				
			</div>
		</div>
    </div>
	
	<div class="container-fluid">
		<div class='row-fluid'>
			<div class='span6'>
				<h2>Settings</h2>
			</div>
			
		</div>
		<div class='row-fluid'>
			<div class='span6'>
				
			</div>
			
		</div>
	</div>
	
	<div class="container-fluid">
		<div class='row-fluid'>
			<div class='span6'>
			<?php

			$guisettingsFile = "config/config.php";
			if (file_exists($guisettingsFile)) { 
				require_once(dirname(__FILE__) . '/config/config.php');
			
				// check for a successful form post  
				if (isset($_GET['s'])) {
					echo "<div class=\"alert alert-warning alert-dismissable\">".$_GET['s']."";  
					echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\"><i class=\"icon icon-remove-circle\"></i></button></div>";
				// check for a form error  
				}elseif (isset($_GET['e'])) {
					echo "<div class=\"alert alert-warning alert-dismissable\">".$_GET['e']."";
					echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\"><i class=\"icon icon-remove-circle\"></i></button></div>";
				}
			?>
			

			
			
			
			<div class='wellbg'>
				<div class='wellheader'>
					<div class='dashboard-wellheader'>
					<h3>General</h3>
					</div>
				</div>
				<h4>plexWatch Version: <strong><?php echo $plexWatch['version'] ?></strong></h4><br>
				<form action="includes/process_settings.php" method="POST">
				<fieldset>
				<div class="form-group-overlay">
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsIp">PMS IP Address</label>
					  <div class="controls">
						<input id="pmsIp" name="pmsIp" type="text" placeholder="0.0.0.0" class="input-xlarge" required="" value="<?php echo $plexWatch['pmsIp'] ?>">
						<p class="help-block">Plex Media Server IP address, hostname or domain name</p>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsHttpPort">PMS Web Port</label>
					  <div class="controls">
						<input id="pmsHttpPort" name="pmsHttpPort" type="text" placeholder="32400" class="input-small" required="" value="<?php echo $plexWatch['pmsHttpPort'] ?>">
						<p class="help-block">Plex Media Server's web port</p>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsHttpsPort">PMS Secure Web Port</label>
					  <div class="controls">
						<input id="pmsHttpsPort" name="pmsHttpsPort" type="text" placeholder="32443" class="input-small" required="" value="<?php echo $plexWatch['pmsHttpsPort'] ?>">
						<p class="help-block">Plex Media Server's secure web port</p>
					  </div>
					</div>
					
					
					<?php 	
					
					if ($plexWatch['https'] == "no" ) {
						$https = '';
					}else if ($plexWatch['https'] == "yes" ) {
						$https = "checked='yes'";
					}
					?>
					
					<!-- Multiple Checkboxes (inline) -->
					<div class="control-group">
					  <label class="control-label" for="https">Use HTTPS (optional)</label>
					  <div class="controls">
						<label class="checkbox inline" for="https-0">
						  <input type="checkbox" name="https" id="https-0" value="yes" <?php echo $https ?>">
						  <p class="help-block">Use Plex Media Server's secure web port</p>
						</label>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="plexWatchDb">plexWatch Database</label>
					  <div class="controls">
						  <input id="plexWatchDb" name="plexWatchDb" type="text" placeholder="/opt/plexWatch/plexWatch.db" class="input-xlarge" required="" value="<?php echo $plexWatch['plexWatchDb'] ?>">
						  <p class="help-block">File location of your plexWatch database.</p>
					  </div>
					</div>
					
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="myPlexUser">myPlex Username (optional)</label>
					  <div class="controls">
						  <input id="myPlexUser" name="myPlexUser" type="text" placeholder="" class="input-xlarge" value="<?php echo $plexWatch['myPlexUser'] ?>">
						  <p class="help-block">If you would like to access plexWatch/Web on other networks, a myPlex username and password are required.</p>
					  </div>
					</div>
					
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="myPlexPass">myPlex Password (optional)</label>
					  <div class="controls">
						  <input id="myPlexPass" name="myPlexPass" type="password" placeholder="" class="input-xlarge" value="<?php echo $plexWatch['myPlexPass'] ?>">
						  <p class="help-block">If you would like to access plexWatch/Web on other networks, a myPlex username and password are required.</p>
					  </div>
					</div>
				</div>	
			</div>	
			<div class='wellbg'>	
				
					<div class='wellheader'>
						<div class='dashboard-wellheader'>
						<h3>Grouping</h3>
						</div>
					</div>
				
				<div class="form-group-overlay">					
					
					<?php 	
					
					
					if ($plexWatch['globalHistoryGrouping'] == "no" ) {
						$globalHistoryGrouping = '';
					}else if ($plexWatch['globalHistoryGrouping'] == "yes" ) {
						$globalHistoryGrouping = "checked='yes'";
					}
					
					
					if ($plexWatch['userHistoryGrouping'] == "no" ) {
						$userHistoryGrouping = '';
					}else if ($plexWatch['userHistoryGrouping'] == "yes" ) {
						$userHistoryGrouping = "checked='yes'";
					}
					
					
					if ($plexWatch['chartsGrouping'] == "no" ) {
						$chartsGrouping = '';
					}else if ($plexWatch['chartsGrouping'] == "yes" ) {
						$chartsGrouping = "checked='yes'";
					}

					?>
						 
					<!-- Multiple Checkboxes (inline) -->
					<div class="control-group">
					  <label class="control-label" for="globalHistoryGrouping">Global History (optional)</label>
					  <div class="controls">
						<label class="checkbox inline" for="globalHistoryGrouping">
						  <input type="checkbox" name="globalHistoryGrouping" id="globalHistoryGrouping-0" value="yes" <?php echo $globalHistoryGrouping; ?>>
						  <p class="help-block">Enable global history grouping</p>
						</label>
					<label class="control-label" for="userHistoryGrouping">User History (optional)</label>
						<label class="checkbox inline" for="userHistoryGrouping">
						  <input type="checkbox" name="userHistoryGrouping" id="userHistoryGrouping-0" value="yes" <?php echo $userHistoryGrouping; ?>">
						  <p class="help-block">Enable user history grouping</p>
						</label>
					<label class="control-label" for="chartsGrouping">Charts (optional)</label>	
						<label class="checkbox inline" for="chartsGrouping">
						  <input type="checkbox" name="chartsGrouping" id="chartsGrouping-0" value="yes" <?php echo $chartsGrouping; ?>">
						  <p class="help-block">Enable charts grouping</p>
						</label>
					</div>
					
					</div>
				</div>	
			</div>
				
			
				<div class="form-actions">
				<!-- Button -->
				<div class="control-group">
				  <label class="control-label" for="submit"></label>
				  <div class="controls">
					<button id="submit" name="submit" class="btn btn-medium btn-primary"" value="Save Data">Save</button>
					<a href="index.php"><button type="button" class="btn btn-medium btn-cancel">Cancel</button></a>
				  </div>
				</div>
				</div>
				</fieldset>
				</form>
			
			<?php
			}else{
			?>
			
			<div class='wellbg'>
				<div class='wellheader'>
					<div class='dashboard-wellheader'>
					<h3>General</h3>
					</div>
				</div>
				
				<form action="includes/process_settings.php" method="POST">
				<fieldset>
				<div class="form-group-overlay">
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsIp">PMS IP Address</label>
					  <div class="controls">
						<input id="pmsIp" name="pmsIp" type="text" placeholder="0.0.0.0" class="input-xlarge" required="">
						<p class="help-block">Plex Media Server IP address, hostname or domain name</p>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsHttpPort">PMS Web Port</label>
					  <div class="controls">
						<input id="pmsHttpPort" name="pmsHttpPort" type="text" placeholder="32400" class="input-small" required="">
						<p class="help-block">Plex Media Server's web port</p>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="pmsHttpsPort">PMS Secure Web Port</label>
					  <div class="controls">
						<input id="pmsHttpsPort" name="pmsHttpsPort" type="text" placeholder="32443" class="input-small" required="">
						<p class="help-block">Plex Media Server's secure web port</p>
					  </div>
					</div>

					<!-- Multiple Checkboxes (inline) -->
					<div class="control-group">
					  <label class="control-label" for="https">Use HTTPS</label>
					  <div class="controls">
						<label class="checkbox inline" for="https-0">
						  <input type="checkbox" name="https" id="https-0" value="Use Plex Media Server's secure web port">
						  <p class="help-block">Use Plex Media Server's secure web port</p>
						</label>
					  </div>
					</div>

					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="plexWatchDb">plexWatch Database</label>
					  <div class="controls">
						  <input id="plexWatchDb" name="plexWatchDb" type="text" placeholder="/opt/plexWatch/plexWatch.db" class="input-xlarge" required="">
						  <p class="help-block">File location of your plexWatch database.</p>
					  </div>
					</div>
					
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="myPlexUser">myPlex Username (optional)</label>
					  <div class="controls">
						  <input id="myPlexUser" name="myPlexUser" type="text" placeholder="" class="input-xlarge" >
						  <p class="help-block">In order to access plexWatch/Web on other networks, a myPlex username and password are required.</p>
					  </div>
					</div>
					
					<!-- Text input-->
					<div class="control-group">
					  <label class="control-label" for="myPlexPass">myPlex Password (optional)</label>
					  <div class="controls">
						  <input id="myPlexPass" name="myPlexPass" type="text" placeholder="" class="input-xlarge" >
						  <p class="help-block">In order to access plexWatch/Web on other networks, a myPlex username and password are required.</p>
					  </div>
					</div>
					
					
				</div>	
			</div>	
			<div class='wellbg'>	
				
					<div class='wellheader'>
						<div class='dashboard-wellheader'>
						<h3>Grouping</h3>
						</div>
					</div>
				
				<div class="form-group-overlay">					

					<!-- Multiple Checkboxes (inline) -->
					<div class="control-group">
					  <label class="control-label" for="globalHistoryGrouping">Global History (optional)</label>
					  <div class="controls">
						<label class="checkbox inline" for="globalHistoryGrouping">
						  <input type="checkbox" name="globalHistoryGrouping" id="globalHistoryGrouping-0" value="yes">
						  <p class="help-block">Enable global history grouping</p>
						</label>
					<label class="control-label" for="userHistoryGrouping">User History (optional)</label>
						<label class="checkbox inline" for="userHistoryGrouping">
						  <input type="checkbox" name="userHistoryGrouping" id="userHistoryGrouping-0" value="yes">
						  <p class="help-block">Enable user history grouping</p>
						</label>
					<label class="control-label" for="chartsGrouping">Charts (optional)</label>	
						<label class="checkbox inline" for="chartsGrouping">
						  <input type="checkbox" name="chartsGrouping" id="chartsGrouping-0" value="yes">
						  <p class="help-block">Enable charts grouping</p>
						</label>
					</div>
					
					</div>
				</div>	
			</div>
				
			
				<div class="form-actions">
				<!-- Button -->
				<div class="control-group">
				  <label class="control-label" for="submit"></label>
				  <div class="controls">
					<button id="submit" name="submit" class="btn btn-medium btn-primary"" value="Save Data">Save</button>
					<a href="index.php"><button type="button" class="btn btn-medium btn-cancel">Cancel</button></a>
				  </div>
				</div>
				</div>
				</fieldset>
				</form>
			<?php
			}
			
			if (!file_exists($guisettingsFile)) {
						
			?>
			
				<div id="welcomeModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				  <div class="modal-header">
					
					<h2 id="myModalLabel"><i class="icon-large icon-chevron-right icon-white"></i> Get Started</h2>
				  </div>
				  <div class="modal-body">
					<img src="images/logo-plexWatch-welcome.png"></img>
					<h1>Welcome to plexWatch/Web</h1>
					
					<p>PlexWatch/Web makes it easy to view in-depth historical statistics and activity of your Plex Media Server. Let's get started by checking for some requirements.</p>
					<?php
						$sqliteVer = SQLite3::version();
						if (isset($_SERVER['SERVER_SOFTWARE'])) {
							echo "<li><i class='icon icon-ok'></i> Web Server: <strong><span class='label label-success'>".$_SERVER['SERVER_SOFTWARE']."</strong></span></li>";
						}else{
							echo "<li><i class='icon icon-warning-sign'></i> Web Server: <strong><span class='label label-important'>No information available</strong></span></li>";
						}
						$phpVersion = phpversion();
						if (!empty($phpVersion)) {
							echo "<li><i class='icon icon-ok'></i> PHP Version: <strong><span class='label label-success'>v".phpversion()."</strong></span></li>";
						}else{
							echo "<li><i class='icon icon-warning-sign'></i> PHP Version: <strong><span class='label label-important'>No information available</strong></span></li>";
						}
						$sqliteVer = SQLite3::version();
						if (!empty($sqliteVer)) {
							echo "<li><i class='icon icon-ok'></i> PHP SQLite Support: <strong><span class='label label-success'>v".$sqliteVer['versionString']."</strong></span></li>";
						}else{
							echo "<li><i class='icon icon-warning-sign'></i> PHP SQLite Support: <strong><span class='label label-important'>No information available</strong></span></li>";
						}
							
						?>	

						<br>
						<p><h4>Linux and MAC users:</h4> Please ensure you have installed, configured and tested <a href="http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/">plexWatch v0.1.1</a> or above before continuing.</p>
						<br>
						<p><h4>Windows users:</h4> Please ensure you have installed, configured and tested <a href="http://forums.plexapp.com/index.php/topic/79616-plexwatch-windows-branch/">plexWatch for Windows v0.1.1</a> or above before continuing.</p> 
						<br>
						<p>If all requirements have been met you can move forward by filling in a few key configuration options now.</p>
						<br>

				  </div>
				  
				  <div class="modal-footer">
						<button class="btn btn-primary pull-right" data-dismiss="modal" aria-hidden="true">Nooice!!!<br>I'm ready to go.</button>
				  </div>
				</div>
			<?php
			}
			?>
			
			</div>
				
		</div>			
			
			

		<footer>
		
		</footer>
		
    </div><!--/.fluid-container-->
    
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
	</script>
	
	<script>
	$('#welcomeModal').modal('show')
	</script>
	
	<script>
	$('#actionSubmit').on('click', function (e) {
		e.preventDefault();
		alert($('#groupedHistory').serialize());
	});

	$('.btn-group').button()
	</script>

	
	<script>
	window.setTimeout(function() {
    $(".alert-warning").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
	}, 4000);
	</script>
	
	
	
	
  </body>
</html>
