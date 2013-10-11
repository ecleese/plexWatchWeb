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
				<div class="logo"></div>
				<ul class="nav">
					
					<li><a href="index.php"><i class="icon-home icon-white"></i> Home</a></li>
					<li><a href="history.php"><i class="icon-calendar icon-white"></i> History</a></li>
					<li class="active"><a href="users.php"><i class="icon-user icon-white"></i> Users</a></li>
					<li><a href="charts.php"><i class="icon-list icon-white"></i> Charts</a></li>
					
				</ul>
				
			</div>
		</div>
    </div>
	
	<div class="container-fluid">
		<div class='row-fluid'>
			<div class='span12'>
				
			</div>
		</div>
	</div>
	
	<div class="container-fluid">
		<div class='row-fluid'>
			<div class='span12'>
			<?php
			require_once(dirname(__FILE__) . '/config.php');	

			date_default_timezone_set(@date_default_timezone_get());

			$db = new SQLite3($plexWatch['plexWatchDb']);
			$users = $db->query("SELECT user as users,xml FROM processed GROUP BY user ORDER BY users") or die ("Failed to access plexWatch database. Please check your server and config.php settings.");
		
			echo "<div class='span12'>";
			echo "<div class='wellbg'>";
				echo "<div class='wellheader'>";
					echo "<div class='dashboard-wellheader'>";
					echo "<h3>Users</h3>";
					echo "</div>";
				echo "</div>";
				echo "<ul class='dashboard-users'>";
					// Run through each feed item
				while ($user = $users->fetchArray()) {
				
				$userXml = simplexml_load_string($user['xml']) ;                         
						echo "<li>";
						if (empty($userXml->User['thumb'])) {				
					
							echo "<div class='users-poster-face'><a href='user.php?user=".$user['users']."'><img src='images/gravatar-default-80x80.png'></></a></div>";
						}else{
							echo "<div class='users-poster-face'><a href='user.php?user=".$user['users']."'><img src='".$userXml->User['thumb']."'></></a></div>";
						}
						echo "<div class='clearfix'></div>";
						echo "<div class=dashboard-users-metacontainer>";
						
							echo $user['users'];
						
						echo "</div>";
						echo "</li>";
						
				}
				?>
		</div><!--/.fluid-row-->			
			
			

		<footer>
		
		</footer>
		
    </div><!--/.fluid-container-->
    
    <!-- javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery-2.0.3.js"></script>
	<script src="js/bootstrap.js"></script>
	

  </body>
</html>
