<?php

require_once(dirname(__FILE__) . '/../config/config.php');
require_once(dirname(__FILE__) . '/timeago.php');

if (isset($_GET['width'])) {
  $containerSize = 1; // min size?
  $tmp = $_GET['width']/180;
  if ($tmp > 0) { 
    $containerSize = $tmp; 
    if (!isset($singlerow)) {  $containerSize = $containerSize*1;    }   
  }
  $containerSize = floor($containerSize);
}

/* needs some indentation fixing */

				if ($plexWatch['https'] == 'yes') {
					$plexWatchPmsUrl = "https://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpsPort']."";
				}else if ($plexWatch['https'] == 'no') {
					$plexWatchPmsUrl = "http://".$plexWatch['pmsIp'].":".$plexWatch['pmsHttpPort']."";
				}
			date_default_timezone_set(@date_default_timezone_get());

			if ($plexWatch['myPlexAuthToken'] != '') {
				$myPlexAuthToken = $plexWatch['myPlexAuthToken'];
            if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/library/recentlyAdded?query=c&X-Plex-Container-Start=0&X-Plex-Container-Size=".$containerSize."&X-Plex-Token=".$myPlexAuthToken."")) {
               $recentRequest = simplexml_load_string($fileContents) or die ("<div class='alert alert-warning'>Failed to access Plex Media Server. Please check your settings.</div>");
            }
			}else{
				$myPlexAuthToken = '';
				if ($fileContents = file_get_contents("".$plexWatchPmsUrl."/library/recentlyAdded?query=c&X-Plex-Container-Start=0&X-Plex-Container-Size=".$containerSize)) {
               $recentRequest = simplexml_load_string($fileContents) or die ("<div class='alert alert-warning'>Failed to access Plex Media Server. Please check your settings.</div>");
            }

			}
			
			
				echo "<div class='dashboard-recent-media-row'>";
					echo "<ul class='dashboard-recent-media'>";
						// Run through each feed item
						foreach ($recentRequest->children() as $recentXml) {		              
						
							if ($recentXml['type'] == "season") {
								
									$recentArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentXml['art']."&width=320&height=160";                                        
									$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentXml['thumb']."&width=153&height=225";  
								
								
									echo "<div class='dashboard-recent-media-instance'>";
									echo "<li>";
									
									if($recentXml['thumb']) {
									
										echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
									}else{
										echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
									}
									
									echo "<div class=dashboard-recent-media-metacontainer>";
									
									echo "<h3>Season ".$recentXml['index']."</h3>";
									
									
									$recentTime = $recentXml['addedAt'];
									$timeNow = time();
									$age = time() - strtotime($recentTime);
									
									echo "<h4>Added ".TimeAgo($recentTime)."</h4>";
									
									echo "</div>";
									echo "</li>";
									echo "</div>";

						
							}else if ($recentXml['type'] == "movie") {				
							
								$recentArtUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentXml['art']."&width=320&height=160";                                        
								$recentThumbUrl = "".$plexWatchPmsUrl."/photo/:/transcode?url=http://127.0.0.1:".$plexWatch['pmsHttpPort']."".$recentXml['thumb']."&width=153&height=225";  
								
								
								echo "<div class='dashboard-recent-media-instance'>";
								echo "<li>";
								
								if($recentXml['thumb']) {
									
									echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml['ratingKey']. "'><img src='includes/img.php?img=".urlencode($recentThumbUrl)."' class='poster-face'></img></a></div></div>";
								}else{
									echo "<div class='poster'><div class='poster-face'><a href='info.php?id=" .$recentXml->Video['ratingKey']. "'><img src='images/poster.png' class='poster-face'></img></a></div></div>";
								}
									
								echo "<div class=dashboard-recent-media-metacontainer>";
								$parentIndexPadded = sprintf("%01s", $recentXml['parentIndex']);
								$indexPadded = sprintf("%02s", $recentXml['index']);
								echo "<h3>".$recentXml['title']." (".$recentXml['year'].")</h3>";
									
									
								$recentTime = $recentXml['addedAt'];
								$timeNow = time();
								$age = time() - strtotime($recentTime);
								
								echo "<h4>Added ".TimeAgo($recentTime)."</h4>";
									
								echo "</div>";
								echo "</li>";
								echo "</div>";
							
							}
						
						}
					echo "</ul>";
				echo "</div>";
			echo "</div>";

	
?>
