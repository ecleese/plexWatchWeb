#!/bin/sh

curl -s -d process=login -d page=start -d user=web -d password=web -c cookiecurl.tmp "http://192.168.0.254/dude/main.html" >> dude2web.log
sleep 1
curl -b cookiecurl.tmp -o /var/www/plexWatch/images/charts/cpu.png "http://192.168.0.254/dude/chart.png?page=chart_picture&download=yes&id=19942&type=4&num=0" >> dude2web.log
sleep 1
curl -b cookiecurl.tmp -o /var/www/plexWatch/images/charts/mem.png "http://192.168.0.254/dude/chart.png?page=chart_picture&download=yes&id=19944&type=4&num=0" >> dude2web.log
sleep 1
curl -b cookiecurl.tmp -s "http://192.168.0.254/dude/main.html?page=chart_info&type=0&id=21262" >> dude2web.log
sleep 1
curl -b cookiecurl.tmp -o /var/www/plexWatch/images/charts/bw.png "http://192.168.0.254/dude/chart.png?page=chart_picture&download=yes&id=21262&type=0&num=0" >> dude2web.log
sleep 1
curl -s -b cookiecurl.tmp "http://192.168.0.254/dude/login.html?drop_cookie=true" >> dude2web.log