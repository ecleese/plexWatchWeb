<?php
	$guisettingsFile = "config/config.php";
    
    if (file_exists($guisettingsFile))
    {
        require_once(dirname(__FILE__) . '/config/config.php');
    }

    
    if (file_exists($guisettingsFile))
    {
        
        if ($plexWatch['https'] == 'yes')
        {
            $plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
        }
        else
        if ($plexWatch['https'] == 'no')
        {
            $plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
        }

        
        if ($plexWatch['myPlexAuthToken'] != '')
        {
            $myPlexAuthToken = $plexWatch['myPlexAuthToken'];
        }
        else
        {
            $myPlexAuthToken = '';
        }

        $db = dbconnect();
        $startDate = $db->querySingle("SELECT min(date(time, 'unixepoch','localtime')) as startdate FROM processed");
        $databaseFile = $plexWatch['plexWatchDb'];
        
        if ($plexWatch['dbHeaderInfo'] == 'yes')
        {
            //set time of DB update, and max time for offline
            $plexWatchhDBlastUpdateTime = date ('F d Y H:i:s', filemtime($databaseFile));
            
            if ($plexWatch['plexWatchDbMin'] != '')
            {
                $plexWatchhDBtimeForOffline = date('F d Y H:i:s', strToTime('-'.($plexWatch['plexWatchDbMin']).' min'));
                //convert times to strings for compare
                $d1 = strtotime($plexWatchhDBlastUpdateTime);
                $d2 = strtotime($plexWatchhDBtimeForOffline);
                $d3 = strToTime(date('F d Y H:i:s'));
                //time difference should never be negative, or it's offline
                $d4 = ($d1 - $d2);
                
                if ($d4 < 0)
                {
                    $dbStatus = "<font color='red'><b>Offline</b></font>&nbsp;&nbsp;";
                    $lastUpdate = $plexWatchhDBlastUpdateTime;
                }
                else
                {
                    $dbStatus = "<font color='green'>Online</font>";
                    $lastUpdate = ($d3 - $d1). " seconds ago";
                }

            }
            else
            {
                $dbStatus = "<font color='red'><b>plexWatch DB update interval not defined in settings!</b></font>";
                $lastUpdate = $plexWatchhDBlastUpdateTime;
            }

            $plexWatchhDBheader = "<span style='font-size: 70%; display: inline-block; padding-left: 20px; padding-top: 2px; line-height: 15px;'><b>Statistics Since:</b> ".date('F d, Y', strtotime($startDate))."<br><b>Database:</b> ".$databaseFile."<br><b>Status:</b> ".$dbStatus."<br><b>Last Update:</b> ".$lastUpdate."</span>";
        }
        else
        if ($plexWatch['dbHeaderInfo'] == 'no')
        {
            $plexWatchhDBheader = "";
        }

    }

    ?>
<div class="container">
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<a href="index.php"><div class="logo hidden-phone"></div></a>
				<?php  echo $plexWatchhDBheader; ?>
				<ul class="nav">
					<li><a href="index.php"><i class="icon-2x icon-home icon-white" data-toggle="tooltip" data-placement="bottom" title="Home" id="home"></i></a></li>
					<li><a href="history.php"><i class="icon-2x icon-calendar icon-white" data-toggle="tooltip" data-placement="bottom" title="History" id="history"></i></a></li>
					<li class="active"><a href="stats.php"><i class="icon-2x icon-tasks icon-white" data-toggle="tooltip" data-placement="bottom" title="Stats" id="stats"></i></a></li>
					<li><a href="users.php"><i class="icon-2x icon-group icon-white" data-toggle="tooltip" data-placement="bottom" title="Users" id="users"></i></a></li>
					<li><a href="charts.php"><i class="icon-2x icon-bar-chart icon-white" data-toggle="tooltip" data-placement="bottom" title="Charts" id="charts"></i></a></li>
					<li><a href="settings.php"><i class="icon-2x icon-wrench icon-white" data-toggle="tooltip" data-placement="bottom" title="Settings" id="settings"></i></a></li>
				</ul>
			</div>
		</div>
</div>