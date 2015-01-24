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
		<div class='row'>	
			<div class='span12'>
				<div class='wellheader'>
					<div class='dashboard-wellheader-no-chevron'>
						<h2><i class="icon-large icon-wrench icon-white"></i> Settings</h2>
					</div>
				</div>	
				
			
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

				
					<div class="row-fluid">
						
					</div>	
					<div class="row-fluid">
					<div class='span3'>
						<ul class="nav nav-list">
							
							<li class="active"><a href="#info">General</a></li>
							<li><a href="#pms">PMS & Database</a></li>
							<li ><a href="#myplex">Plex Authentication</a></li>
							<li ><a href="#grouping">Grouping</a></li>	
						</ul>
					</div>
					
					<div class="span9">
					<form action="includes/process_settings.php" method="POST">
						<fieldset>
						<div class="wellbg">
							<div class="wellheader">
								<div class="dashboard-wellheader">
									<h3><a id="info">Version Information</a></h3>
								</div>
							</div>

							<div class="settings-general-info">
								
								<ul>
									<li>plexWatch/Web Version: <strong>v1.5.4.2</strong></li>	
								
									<?php
									    $db = dbconnect(); 	
										$plexWatchVersion = $db->querySingle("SELECT version FROM config ");
									?>
									<li>plexWatch Version: <strong>v<?php echo $plexWatchVersion ?></strong></li>

								</ul>
							</div>
						</div>
						<div class="wellbg">
							<div class="wellheader">
								<div class="dashboard-wellheader">
									<h3><a id="info">General</a></h3>
								</div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="dateFormat">Date Format</label>
							  <div class="controls">
								<input id="dateFormat" name="dateFormat" type="text" placeholder="m/d/y" class="input-mini" required="" value="<?php echo $plexWatch['dateFormat'] ?>">
								<p class="help-block">The date display format plexWatch/Web should use. <a href="#dateTimeOptionsModal" data-toggle="modal">Date & Time format options</a></p>
							  </div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="timeFormat">Time Format</label>
							  <div class="controls">
								<input id="timeFormat" name="timeFormat" type="text" placeholder="g:i a" class="input-mini" required="" value="<?php echo $plexWatch['timeFormat'] ?>">
								<p class="help-block">The time display format plexWatch/Web should use. <a href="#dateTimeOptionsModal" data-toggle="modal">Date & Time format options</a></p>
							  </div>
							</div>

						</div>

						<div id="dateTimeOptionsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dateTimeOptionsModal" aria-hidden="true">
						<div class="modal-header">	
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>		
	<h3 id="myModalLabel">Date & Time Format Options</h3>
</div>
								
<div class="modal-body">
									
	<div class="span12">
		
	
		<table>

			<tbody>

				<tr><td><h5>Day</h5></td></tr>
				<tr><td align="center"><strong>d</strong></td><td width="300">Numeric, with leading zeros</td><td>01 to 31</td></tr>
				<tr><td align="center"><strong>j</strong></td><td>Numeric, without leading zeros</td><td>1 to 31</td></tr>
				<tr><td align="center"><strong>S</strong></td><td>The English suffix for the day of the month</td><td>st, nd or th in the 1st, 2nd or 15th.</td></tr>
				
				<tr><td><h5>Month</h5></td></tr>
				<tr><td align="center"><strong>m</strong></td><td>Numeric, with leading zeros</td><td>01 to 31</td></tr>
				<tr><td align="center"><strong>n</strong></td><td>Numeric, without leading zeros</td><td>1 to 31</td></tr>
				<tr><td align="center"><strong>F</strong></td><td>Textual full</td><td>January – December</td></tr>
				<tr><td align="center"><strong>M</strong></td><td>Textual three letters</td><td>Jan – Dec</td></tr>
				
				<tr><td><h5>Year</h5></td></tr>
				<tr><td align="center"><strong>Y</strong></td><td>Numeric, 4 digits</td><td>Eg., 1999, 2003</td></tr>
				<tr><td align="center"><strong>y</strong></td><td>Numeric, 2 digits</td><td>Eg., 99, 03</td></tr>

				<tr><td><h5>Time</h5></td></tr>
				<tr><td align="center"><strong>a</strong></td><td width="300">am/pm Lowercase</td><td>am, pm</td></tr>
				<tr><td align="center"><strong>A</strong></td><td>AM/PM Uppercase</td><td>AM, PM</td></tr>
				<tr><td align="center"><strong>g</strong></td><td>Hour, 12-hour, without leading zeros</td><td>1–12</td></tr>
				<tr><td align="center"><strong>h</strong></td><td>Hour, 12-hour, with leading zeros</td><td>01–12</td></tr>
				<tr><td align="center"><strong>G</strong></td><td>Hour, 24-hour, without leading zeros</td><td>0-23</td></tr>
				<tr><td align="center"><strong>H</strong></td><td>Hour, 24-hour, with leading zeros</td><td>00-23</td></tr>
				<tr><td align="center"><strong>i</strong></td><td>Minutes, with leading zeros</td><td>00-59</td></tr>
				<tr><td align="center"><strong>s</strong></td><td>Seconds, with leading zeros</td><td>00-59</td></tr>
				<tr><td align="center"><strong>T</strong></td><td>Timezone abbreviation</td><td>Eg., EST, MDT ...</td></tr>

			</tbody>
			
		</table>
																		
	</div>
	
</div>
										  
<div class="modal-footer">

</div>
</div>

						<div class='wellbg'>
							<div class='wellheader'>
								<div class='dashboard-wellheader'>
								<h3><a id="pms">Plex Media Server & Database Settings</a></h3>
								</div>
							</div>
						

							
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
								  <input type="checkbox" name="https" id="https-0" value="yes" <?php echo $https ?>>
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
							
						</div>	
							
						<div class='wellbg'>
						<div class='wellheader'>
							<div class='dashboard-wellheader'>
							<h3><a id="myplex">Plex Authentication</a></h3>
							</div>
						</div>
							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="myPlexUser">Username (optional)</label>
							  <div class="controls">
								  <input id="myPlexUser" name="myPlexUser" type="text" placeholder="" class="input-xlarge" value="<?php echo $plexWatch['myPlexUser'] ?>">
								  <p class="help-block">If you would like to access plexWatch/Web on other networks, a <a href="https://plex.tv/users/sign_in">Plex.tv</a> username and password are required.</p>
							  </div>
							</div>
							
							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="myPlexPass">Password (optional)</label>
							  <div class="controls">
								  <input id="myPlexPass" name="myPlexPass" type="password" placeholder="" class="input-xlarge" value="">
								  <p class="help-block">If you would like to access plexWatch/Web on other networks, a <a href="https://plex.tv/users/sign_in">Plex.tv</a> username and password are required.</p>
							  </div>
							</div>
						</div>

						<div class='wellbg'>	
					
							<div class='wellheader'>
								<div class='dashboard-wellheader'>
								<h3><a id="grouping">Grouping Settings</a></h3>
								</div>
							</div>
					
											
						
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
								  <input type="checkbox" name="userHistoryGrouping" id="userHistoryGrouping-0" value="yes" <?php echo $userHistoryGrouping; ?>>
								  <p class="help-block">Enable user history grouping</p>
								</label>
							<label class="control-label" for="chartsGrouping">Charts (optional)</label>	
								<label class="checkbox inline" for="chartsGrouping">
								  <input type="checkbox" name="chartsGrouping" id="chartsGrouping-0" value="yes" <?php echo $chartsGrouping; ?>>
								  <p class="help-block">Enable charts grouping</p>
								</label>
							</div>
							
						</div>
						</div>
						
					
					
					
					
					<div class="form-actions">
						<!-- Button -->
						<div class="control-group">
						  <label class="control-label" for="submit"></label>
						  <div class="controls">
							  <div id="friendlyName">
								<button id="submit" name="submit" class="btn btn-medium btn-primary" value="save">Save</button>
								<a href="index.php"><button type="button" class="btn btn-medium btn-cancel">Cancel</button></a>
								</div>
						  </div>
						</div>
						
					</div>
					</fieldset>
					</form>
						
				
					
					
			</div>
			
			
		</div>
	</div>
			
			
				
				
				<?php
				}else{
					if(!class_exists('SQLite3'))
					die("<div class=\"alert alert-warning \">php5-sqlite is not installed. Please install this requirement and restart your webserver before continuing.</div>");
				?>
			
				<div class="wellbg">
					<div class="row-fluid">
						
					</div>	
					<div class="row-fluid">
					<div class='span3'>
						<ul class="nav nav-list">
							
							<li class="active"><a href="#info">General</a></li>
							<li><a href="#pms">PMS & Database</a></li>
							<li ><a href="#myplex">Plex Authentication</a></li>
							<li ><a href="#grouping">Grouping</a></li>	
						</ul>
					</div>
					
					<div class="span9">
					<form action="includes/process_settings.php" method="POST">
						<fieldset>
						
						<div class="wellbg">
							<div class="wellheader">
								<div class="dashboard-wellheader">
									<h3><a id="info">General</a></h3>
								</div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="dateFormat">Date Format</label>
							  <div class="controls">
								<input id="dateFormat" name="dateFormat" type="text" placeholder="m/d/y" class="input-mini" required="" value="m/d/Y">
								<p class="help-block">The date display format plexWatch/Web should use. <a href="#dateTimeOptionsModal" data-toggle="modal">Date & Time format options</a></p>
							  </div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="timeFormat">Time Format</label>
							  <div class="controls">
								<input id="timeFormat" name="timeFormat" type="text" placeholder="g:i a" class="input-mini" required="" value="g:i a">
								<p class="help-block">The time display format plexWatch/Web should use. <a href="#dateTimeOptionsModal" data-toggle="modal">Date & Time format options</a></p>
							  </div>
							</div>

						</div>

						<div id="dateTimeOptionsModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dateTimeOptionsModal" aria-hidden="true">
						<div class="modal-header">	
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon icon-remove"></i></button>		
	<h3 id="myModalLabel">Date & Time Format Options</h3>
</div>
								
<div class="modal-body">
									
	<div class="span12">
		
	
		<table>

			<tbody>

				<tr><td><h5>Day</h5></td></tr>
				<tr><td align="center"><strong>d</strong></td><td width="300">Numeric, with leading zeros</td><td>01 to 31</td></tr>
				<tr><td align="center"><strong>j</strong></td><td>Numeric, without leading zeros</td><td>1 to 31</td></tr>
				<tr><td align="center"><strong>S</strong></td><td>The English suffix for the day of the month</td><td>st, nd or th in the 1st, 2nd or 15th.</td></tr>
				
				<tr><td><h5>Month</h5></td></tr>
				<tr><td align="center"><strong>m</strong></td><td>Numeric, with leading zeros</td><td>01 to 31</td></tr>
				<tr><td align="center"><strong>n</strong></td><td>Numeric, without leading zeros</td><td>1 to 31</td></tr>
				<tr><td align="center"><strong>F</strong></td><td>Textual full</td><td>January – December</td></tr>
				<tr><td align="center"><strong>M</strong></td><td>Textual three letters</td><td>Jan – Dec</td></tr>
				
				<tr><td><h5>Year</h5></td></tr>
				<tr><td align="center"><strong>Y</strong></td><td>Numeric, 4 digits</td><td>Eg., 1999, 2003</td></tr>
				<tr><td align="center"><strong>y</strong></td><td>Numeric, 2 digits</td><td>Eg., 99, 03</td></tr>

				<tr><td><h5>Time</h5></td></tr>
				<tr><td align="center"><strong>a</strong></td><td width="300">am/pm Lowercase</td><td>am, pm</td></tr>
				<tr><td align="center"><strong>A</strong></td><td>AM/PM Uppercase</td><td>AM, PM</td></tr>
				<tr><td align="center"><strong>g</strong></td><td>Hour, 12-hour, without leading zeros</td><td>1–12</td></tr>
				<tr><td align="center"><strong>h</strong></td><td>Hour, 12-hour, with leading zeros</td><td>01–12</td></tr>
				<tr><td align="center"><strong>G</strong></td><td>Hour, 24-hour, without leading zeros</td><td>0-23</td></tr>
				<tr><td align="center"><strong>H</strong></td><td>Hour, 24-hour, with leading zeros</td><td>00-23</td></tr>
				<tr><td align="center"><strong>i</strong></td><td>Minutes, with leading zeros</td><td>00-59</td></tr>
				<tr><td align="center"><strong>s</strong></td><td>Seconds, with leading zeros</td><td>00-59</td></tr>
				<tr><td align="center"><strong>T</strong></td><td>Timezone abbreviation</td><td>Eg., EST, MDT ...</td></tr>

			</tbody>
			
		</table>
																		
	</div>
	
</div>
										  
<div class="modal-footer">

</div>
</div>
						
						<div class='wellbg'>
							<div class='wellheader'>
								<div class='dashboard-wellheader'>
								<h3><a id="pms">Plex Media Server & Database Settings</a></h3>
								</div>
							</div>
						
						
							<form action="includes/process_settings.php" method="POST">
							
							
							
							<fieldset>
							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="pmsIp">PMS IP Address</label>
							  <div class="controls">
								<input id="pmsIp" name="pmsIp" type="text" placeholder="0.0.0.0" class="input-xlarge" required="" >
								<p class="help-block">Plex Media Server IP address, hostname or domain name</p>
							  </div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="pmsHttpPort">PMS Web Port</label>
							  <div class="controls">
								<input id="pmsHttpPort" name="pmsHttpPort" type="text" placeholder="32400" class="input-small" required="" >
								<p class="help-block">Plex Media Server's web port</p>
							  </div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="pmsHttpsPort">PMS Secure Web Port</label>
							  <div class="controls">
								<input id="pmsHttpsPort" name="pmsHttpsPort" type="text" placeholder="32443" class="input-small" required="" >
								<p class="help-block">Plex Media Server's secure web port</p>
							  </div>
							</div>
							
							
							
							
							<!-- Multiple Checkboxes (inline) -->
							<div class="control-group">
							  <label class="control-label" for="https">Use HTTPS (optional)</label>
							  <div class="controls">
								<label class="checkbox inline" for="https-0">
								  <input type="checkbox" name="https" id="https-0" value="yes" >
								  <p class="help-block">Use Plex Media Server's secure web port</p>
								</label>
							  </div>
							</div>

							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="plexWatchDb">plexWatch Database</label>
							  <div class="controls">
								  <input id="plexWatchDb" name="plexWatchDb" type="text" placeholder="/opt/plexWatch/plexWatch.db" class="input-xlarge" required="" >
								  <p class="help-block">File location of your plexWatch database.</p>
							  </div>
							</div>
							
						</div>	
							
						<div class='wellbg'>
						<div class='wellheader'>
							<div class='dashboard-wellheader'>
							<h3><a id="myplex">Plex Authentication</a></h3>
							</div>
						</div>
							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="myPlexUser">Username (optional)</label>
							  <div class="controls">
								  <input id="myPlexUser" name="myPlexUser" type="text" placeholder="" class="input-xlarge" >
								  <p class="help-block">If you would like to access plexWatch/Web on other networks, a <a href="https://plex.tv/users/sign_in">Plex.tv</a> username and password are required.</p>
							  </div>
							</div>
							
							<!-- Text input-->
							<div class="control-group">
							  <label class="control-label" for="myPlexPass">Password (optional)</label>
							  <div class="controls">
								  <input id="myPlexPass" name="myPlexPass" type="password" placeholder="" class="input-xlarge" >
								  <p class="help-block">If you would like to access plexWatch/Web on other networks, a <a href="https://plex.tv/users/sign_in">Plex.tv</a> username and password are required.</p>
							  </div>
							</div>
						</div>

						<div class='wellbg'>	
					
							<div class='wellheader'>
								<div class='dashboard-wellheader'>
								<h3><a id="grouping">Grouping Settings</a></h3>
								</div>
							</div>
					
											
						
							
							 
							<!-- Multiple Checkboxes (inline) -->
							<div class="control-group">
							  <label class="control-label" for="globalHistoryGrouping">Global History (optional)</label>
							  <div class="controls">
								<label class="checkbox inline" for="globalHistoryGrouping">
								  <input type="checkbox" name="globalHistoryGrouping" id="globalHistoryGrouping-0" value="yes" >
								  <p class="help-block">Enable global history grouping</p>
								</label>
							<label class="control-label" for="userHistoryGrouping">User History (optional)</label>
								<label class="checkbox inline" for="userHistoryGrouping">
								  <input type="checkbox" name="userHistoryGrouping" id="userHistoryGrouping-0" value="yes" >
								  <p class="help-block">Enable user history grouping</p>
								</label>
							<label class="control-label" for="chartsGrouping">Charts (optional)</label>	
								<label class="checkbox inline" for="chartsGrouping">
								  <input type="checkbox" name="chartsGrouping" id="chartsGrouping-0" value="yes" >
								  <p class="help-block">Enable charts grouping</p>
								</label>
							</div>
							
						</div>
						</div>
						
					
					
					
					
					<div class="form-actions">
						<!-- Button -->
						<div class="control-group">
						  <label class="control-label" for="submit"></label>
						  <div class="controls">
							  <div id="friendlyName">
								<button id="submit" name="submit" class="btn btn-medium btn-primary" value="save">Save</button>
								<a href="index.php"><button type="button" class="btn btn-medium btn-cancel">Cancel</button></a>
								</div>
						  </div>
						</div>
						
					</div>
					</fieldset>
					</form>
						
				</div>
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
						$sqliteVersion = SQLite3::version();
						if (!empty($sqliteVersion)) {
							echo "<li><i class='icon icon-ok'></i> PHP SQLite Support: <strong><span class='label label-success'>v".$sqliteVersion['versionString']."</strong></span></li>";
						}else{
							echo "<li><i class='icon icon-warning-sign'></i> PHP SQLite Support: <strong><span class='label label-important'>No information available</strong></span></li>";
						}
						
						$curlVersion = curl_version();
						echo "<li><i class='icon icon-ok'></i> PHP Curl Support: <strong><span class='label label-success'>" .$curlVersion['version']. "</span></strong>  / SSL Support: <strong><span class='label label-success'>" .$curlVersion['ssl_version']."</strong></span></li>";	
						
						
						$json[] = '{"Yes":""}';
						foreach ($json as $string) {
							
							json_decode($string);

							switch (json_last_error()) {
								case JSON_ERROR_NONE:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-success'>Yes</span></strong></li>";	
									break;
								case JSON_ERROR_DEPTH:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>Maximum stack depth exceeded</span></strong></li>";
									break;
								case JSON_ERROR_STATE_MISMATCH:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>Underflow or the modes mismatch</span></strong></li>";
									break;
								case JSON_ERROR_CTRL_CHAR:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>Unexpected control character found</span></strong></li>";
									break;
								case JSON_ERROR_SYNTAX:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>Syntax error, malformed JSON</span></strong></li>";
									break;
								case JSON_ERROR_UTF8:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>Malformed UTF-8 characters, possibly incorrectly encoded</span></strong></li>";
									break;
								default:
									echo "<li><i class='icon icon-ok'></i> PHP JSON Support: <strong><span class='label label-important'>No (Unknown Error)</span></strong></li>";
									break;
							}
						}
						
						
						
						echo "<li><i class='icon icon-ok'></i> Your server's timezone: <strong><span class='label label-warning'>".@date_default_timezone_get()."</strong></span></li>";	
						
						
						
					?>	

						<br>
						<p><h4>Note: </h4>Please ensure you have installed, configured and tested <a href="https://github.com/ljunkie/plexWatch">plexWatch v0.1.6</a> or above before continuing. If all requirements above are green and the timezone shown matches your timezone you can move forward by filling in a few key configuration options now.</p>
						<br>

				  </div>
				  
				  <div class="modal-footer">
						<button class="btn btn-primary pull-right" data-dismiss="modal" aria-hidden="true">I'm ready to go.</button>
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
	$(document).ready(function() {
		$('#stats').tooltip();
	});
	</script>
	
	<script>
	$('#welcomeModal').modal('show')
	</script>

	<script>
	$('#dateTimeModal').modal('show')
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
	}, 5000);
	</script>
	
	
  </body>
</html>
