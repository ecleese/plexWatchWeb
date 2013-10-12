<?php


//	plexWatchWeb config file

$plexWatch['https'] = 'no';										// Use Plex Media Server https port (yes or no). If 'yes', keep in mind your browser may initially throw a securty certificate exception and images may not load until you except this.
$plexWatch['pmsIp'] = '192.168.0.50';							// Plex Media Server IP, hostname, or domain name
$plexWatch['pmsHttpPort'] = '32400';							// Plex Media Server HTTP port
$plexWatch['pmsHttpsPort']	= '32443';							// Plex Media Server HTTPS port
$plexWatch['plexWatchDb'] = '/opt/plexWatch/plexWatch.db';		// Location of your plexWatch database



?>