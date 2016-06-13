#!/usr/bin/env bash

# Variables
#------------
mysqlUser='root'
mysqlPass='vagrant'
mysqlAppDbName='cherbouquin'
mysqlAppDbUser='cherbouquin'
mysqlAppDbPass='cherbouquin'
mysqlDumpFile=/vagrant/cherbouquin.sql
vHostName='cherbouquin'
vHostRootFolder='/var/www/public'

# System utilities
# --------------------
apt-get update
apt-get install -y make
apt-get install -y git-core
apt-get install -y curl
apt-get install -y vim
sudo updatedb # to enable locate

# Apache & PHP
# --------------------
apt-get install -y apache2 php5 libapache2-mod-php5
apt-get install -y php5-mysql php5-curl php5-gd php5-intl php-pear php5-imap php5-mcrypt php5-ming php5-ps php5-pspell
apt-get install -y php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl php-apc php5-xdebug
echo "extension=apc.so" | tee -a /etc/php5/apache2/php.ini
a2enmod rewrite

# Set Xdebug config
# --------------------
echo "xdebug.remote_enable = on" | tee -a /etc/php5/apache2/conf.d/xdebug.ini
echo "xdebug.remote_connect_back = on" | tee -a /etc/php5/apache2/conf.d/xdebug.ini
echo "xdebug.idekey = "vagrant"" | tee -a /etc/php5/apache2/conf.d/xdebug.ini
echo "xdebug.remote_host = 192.168.76.1" | tee -a /etc/php5/apache2/conf.d/xdebug.ini

# Set php.ini variables for development environment
# --------------------
sudo sed -i "s/^display_errors.*$/display_errors = On/g" /etc/php5/apache2/php.ini
sudo sed -i "s/^display_startup_errors.*$/display_startup_errors = On/g" /etc/php5/apache2/php.ini
sudo sed -i "s/^error_reporting.*$/error_reporting = E_ALL/g" /etc/php5/apache2/php.ini
sudo sed -i "s/^html_errors.*$/html_errors = On/g" /etc/php5/apache2/php.ini
sudo sed -i "s/^log_errors.*$/log_errors = On/g" /etc/php5/apache2/php.ini
sudo sed -i "s/^track_errors.*$/track_errors = On/g" /etc/php5/apache2/php.ini

service apache2 restart

# Setup web dir
# --------------------
rm -rf /var/www
ln -fs /vagrant /var/www

## Mysql
## --------------------

# Ignore the post install questions
export DEBIAN_FRONTEND=noninteractive
# Install MySQL
apt-get -y install mysql-server-5.5
apt-get -y install phpmyadmin
echo "Include /etc/phpmyadmin/apache.conf" | tee -a /etc/apache2/apache2.conf
mysqladmin -u ${mysqlUser} password ${mysqlPass}
service apache2 restart
# Create database and user
mysql -u ${mysqlUser} -p"${mysqlPass}" << EOF
CREATE DATABASE ${mysqlAppDbName};
CREATE USER '${mysqlAppDbUser}'@'localhost' IDENTIFIED BY '${mysqlAppDbUser}';
GRANT ALL PRIVILEGES ON ${mysqlAppDbName}. * TO '${mysqlAppDbUser}'@'localhost';
EOF
# Import database
mysql -u ${mysqlUser} -p"${mysqlPass}" ${mysqlAppDbName} < ${mysqlDumpFile}

# VHosts
# --------------------
# Create the file with VirtualHost configuration in /etc/apache2/site-available/
sudo echo "<VirtualHost *:80>
    DocumentRoot ${vHostRootFolder}
    ServerName ${vHostName}.local.com
    <Directory ${vHostRootFolder}>
        Options +Indexes +FollowSymLinks +MultiViews +Includes
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>
</VirtualHost>" > /etc/apache2/sites-available/${vHostName}.conf
# Add the host to the hosts file
sudo echo 127.0.0.1 ${vHostName}.local.com >> /etc/hosts
# Enable the site
sudo a2ensite ${vHostName}
# Reload Apache2
sudo /etc/init.d/apache2 restart
