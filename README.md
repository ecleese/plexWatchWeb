plexWatch/Web - 0.0.1
=====================

A web front-end for plexWatch.

* https://github.com/ljunkie/plexWatch
* http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/


###Requirements
---------------
* a web server that supports php (apache, nginx, etc)
* php5
* php5-sqlite


### Install 
-----------

1. Install requirements
2. Download and unzip the plexWatchWeb package.
3. Edit config.php file

	```
	sudo nano /var/www/plexWatch/config.php
	```
  	* Modify Variables as needed

	```
	php 
	$plexWatch['pmsUrl'] = '0.0.0.0';							// Plex Media Server IP or hostname
	$plexWatch['plexWatchDb'] = '/opt/plexWatch/plexWatch.db';	// Location of your plexWatch database 
	```

4. Upload the contents to the desired location on your web server "/var/www/plexwatch"


###Use
------

Navigate to: http://ip-of-web-server/plexwatch

