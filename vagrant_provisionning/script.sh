#!/bin/sh



#sudo yum install httpd
#sudo yum install php
#sudo yum install php-apc
#sudo yum install php-mbstring
#sudo yum install mysql

# copy virtual host file
#cp /vagrant/vagrant_provisionning/cherbouquin.conf /etc/httpd/conf.d/cherbouquin.conf

# copy apache conf file
#cp /vagrant/vagrant_provisionning/httpd.conf /etc/httpd/conf/httpd.conf

# install apc
#yum install php-pear php-devel httpd-devel pcre-devel gcc make
#pecl install apc
#echo "extension=apc.so" > /etc/php.d/apc.ini
#sudo yum -y install php-pecl-apc
#sudo yum install php-devel

#sudo yum -y install php-mbstring

# copy php.ini conf file
#cp /vagrant/vagrant_provisionning/php.ini /etc/php.ini

#sudo service httpd reload

# create database
echo "create database share1book" | mysql -u homestead -psecret
mysql -u homestead -psecret -h localhost share1book < "/home/vagrant/Code/vagrant_provisionning/share1book.sql"

# create database user
echo "grant usage on *.* to share1book@localhost identified by 'share1book';" | mysql -u homestead -psecret
echo "grant all privileges on share1book.* to share1book@localhost ;" | mysql -u homestead -psecret
