plexWatch/Web - 0.0.2
=====================

A web front-end for plexWatch.

* https://github.com/ljunkie/plexWatch
* http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/


###Requirements
---------------
* a web server that supports php (apache, nginx, xampp, WampServer, EasyPHP, etc)
* php5
* php5-sqlite


### Install 
-----------

1. Install requirements
2. Download and unzip the plexWatchWeb package.
3. Edit config.php file
 * Modify Variables as needed

	```
	$plexWatch['pmsUrl'] = '0.0.0.0';							// Plex Media Server IP or hostname
	$plexWatch['plexWatchDb'] = '/opt/plexWatch/plexWatch.db';	// Location of your plexWatch database 
	```
4. Upload the contents to the desired location on your web server "/var/www/plexWatch"

  	




###Use
------

Navigate to: http://ip-of-web-server/plexWatch
