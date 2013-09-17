plexWatch/Web - 0.0.1
=====================

A web front-end for plexWatch.

https://github.com/ljunkie/plexWatch
http://forums.plexapp.com/index.php/topic/72552-plexwatch-plex-notify-script-send-push-alerts-on-new-sessions-and-stopped/


###Requirements
---------------
* php5
* php5-sqlite


### Install 
-----------

1. Install requirements

* Debian/Ubuntu

```
sudo apt-get install php5 php5-sqlite php5-mysql
sudo apt-get install php-pear php-apc php5-curl
sudo apt-get autoremove
sudo apt-get install php5-sqlite
sudo apt-get install libapache2-mod-fastcgi php5-fpm php5

sudo service apache2 restart
```
For more help, visit https://www.digitalocean.com/community/articles/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu

2. Download and unzip the plexWatchWeb package.
 
3. Edit config.php file
```sudo nano /var/www/plexWatch/config.php
```
    * Modify Variables as needed
```php 
    $plexWatch['pmsUrl'] = '0.0.0.0';						// Plex Media Server IP or hostname
	$plexWatch['plexWatchDb'] = '/opt/plexWatch/plexWatch.db';		// Location of your plexWatch database 
```

4. Upload the contents to the desired location on your web server "/var/www/plexwatch"


###Use
------

Navigate to: http://ip-of-web-server/plexwatch

