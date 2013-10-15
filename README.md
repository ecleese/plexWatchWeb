plexWatch/Web - 0.0.3
=====================

A web front-end for plexWatch.

* plexWatch: https://github.com/ljunkie/plexWatch
* plexWatch Plex forum thread: http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/
* plexWatch (Windows branch) Plex forum thread: http://forums.plexapp.com/index.php/topic/79616-plexwatch-windows-branch/

###Features
-----------
* Responsive web design viewable on desktop, tablet and mobile web browsers 

* Themed to complement Plex/Web 

* Status activity of Plex Media Server's ports (online/offline)

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
	* ip address (if enabled in plexWatch)
	* title
	* start time
	* paused duration length
	* stop time
	* duration length
	* percentage completed
	
* full user list with gravatar

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
	- movies (includes watching history)
	- tv shows (includes top 10 watched episodes)
	- tv seasons
	- tv episodes (includes watching history)


###Requirements
---------------
* Plex Media Server (v0.9.8+) and a PlexPass membership
* plexWatch (v0.1.0+)
* a web server that supports php (apache, nginx, XAMPP, WampServer, EasyPHP, lighttpd, etc)
* php5
* php5-sqlite


### Install 
-----------

1. Install requirements
2. Download and unzip the plexWatchWeb package.
3. Edit config.php file
 * Modify Variables as needed

	```
	$plexWatch['https'] = 'no';										// Use Plex Media Server https port (yes or no). If 'yes', keep in mind your browser may initially throw a securty certificate exception and images may not load until you except this.
	$plexWatch['pmsIp'] = '0.0.0.0';								// Plex Media Server IP, hostname, or domain name
	$plexWatch['pmsHttpPort'] = '32400';							// Plex Media Server HTTP port
	$plexWatch['pmsHttpsPort']	= '32443';							// Plex Media Server HTTPS port
	$plexWatch['plexWatchDb'] = '/opt/plexWatch/plexWatch.db';		// Location of your plexWatch database 
	```
4. Upload the contents to the desired location on your web server "/var/www/plexWatch"


###Use
------

Navigate to: http://ip-of-web-server/plexWatch

