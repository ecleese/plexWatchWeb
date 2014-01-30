plexWatch/Web - v1.5.0.19 dev
=============================

A web front-end for plexWatch.

* Original plexWatch: <https://github.com/ljunkie/plexWatch>
* plexWatch Plex forum thread: <http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped>
* plexWatch (Windows branch) Plex forum thread: <http://forums.plexapp.com/index.php/topic/79616-plexwatch-windows-branch>


###Support
-----------
* plexWatch/Web Wiki: <https://github.com/ecleese/plexWatchWeb/wiki>
* plexWatch/Web Plex forum thread: <https://forums.plex.tv/index.php/topic/82819-plexwatchweb-a-web-front-end-for-plexwatch/?p=566929>


###Features
-----------

All of the features of plexWatch/Web (detailed below), plus these features:

**Main Page**

* Total Plays and Total Data Transferred in statistics (grouped only)<br> 
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-81343500-1391024428.png)
	
**History Page**

* Data transferred column added to chart (media size * percentage watched)
* Start/Stop time info added to stream info
* Media size added to stream info<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-53204400-1391024553_thumb.png)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-69240800-1391024554_thumb.png)

**Stats Page**

* Daily, Weekly, and Monthly bandwidth charts (grouped only)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-72732400-1391024858_thumb.png)

**Users Page**

* Plays per User charts (24 hr, 7 day, 30 day)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-13521300-1391024863_thumb.png)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-83910500-1391024865_thumb.png)

**User Page**

* Total Data Transferred added to user global stats
* Data column added to user history chart (same as history page)
* Media size added to stream info (same as history page)
* User plays chart added to user history tab<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-27737000-1391025121_thumb.png)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-06434800-1391025123_thumb.png)

**Global - header.php**

* Created header.php with navigation code
* Added database statistics to header - shows the status of your database<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-31275200-1391025306.png)<br>![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-95592800-1391025306.png)

**Settings Page**

* Added options to show/hide DB statistics in header
* Added option to specify how often your plexWatch DB is updated (to calculate status)<br>
![ScreenShot](https://forums.plex.tv/uploads/monthly_01_2014/post-141806-0-79077100-1391025307_thumb.png)


-----------


* Responsive web design viewable on desktop, tablet and mobile web browsers 

* Themed to complement Plex/Web 

* Easy configuration setup via html form

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

* Global watching history charts (hourly, daily, monthly)

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
	
* full user list with general information and comparison stats

* individual user information
	- username and gravatar (if available)
	- daily, weekly, monthly, all time stats for play count and duration length
	- individual platform stats for each user
	- public ip address history with last seen date and geo tag location 
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
* plexWatch (v0.1.6+)
* a web server that supports php (apache, nginx, XAMPP, WampServer, EasyPHP, lighttpd, etc)
* php5
* php5-sqlite
* php5-curl
* php5-json


### Install 
-----------

1. Install requirements
2. Download and unzip the plexWatchWeb package.
3. Upload the contents to the desired location on your web server "/var/www/plexWatch"


###Use
------

Navigate to: http://ip-of-web-server/plexWatch
