plexWatch/Web - 0.0.1
=====================

A web front-end for plexWatch.

* https://github.com/ljunkie/plexWatch
* http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/

###Features
-----------
* Responsive web design viewable on desktop, tablet and mobile web browsers 

* Status activity of Plex Media Server's ports (http, https)

* Current Plex Media Server viewing activity including:
	* number of current users
	* title
	* progress
	* platform
	* user
	* state (playing, paused, buffering, etc)
	* stream type (direct, transcoded)
	* video type & resolution
	* audio type & channel count.
	
* Recently added media and how long ago it was added

* Global watching history with search/filtering & dynamic column sorting
	* date
	* user
	* platform
	* ip address
	* title
	* start time
	* paused duration length
	* stop time
	* duration length
	* percentage completed
	
* user list

* individual user information
	- username and gravatar (if available)
	- daily, weekly, monthly, all time stats for play count and duration length
	- recently watched content
	- watching history
	
* charts
	- top 10 all time viewed content
	- top 10 viewed movies
	- top 10 viewed tv shows
	- top 10 viewed tv episodes
	
* content information pages 
	- movies including watching history
	- tv shows
	- tv seasons
	- tv episodes including watching history

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

