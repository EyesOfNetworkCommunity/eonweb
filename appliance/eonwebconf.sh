#!/bin/sh

# Define values
eonconfdir="/srv/eyesofnetworkconf/eonweb"
eondir="/srv/eyesofnetwork"
datadir="$eondir/eonweb"
eonwebdb="eonweb"
nagiosbpdb="nagiosbp"
snmpdir="/etc/snmp"
backupdir="/etc"

# change right acces for this files
chmod 777 ${datadir}/cache
chmod 666 ${snmpdir}/snmpd.conf
chmod 666 ${snmpdir}/snmptrapd.conf
chmod 666 ${backupdir}/backup-manager.conf

# change own user for eonweb directory
chown -R root:eyesofnetwork ${datadir}*

# create the eonweb database
mysqladmin -u root --password=root66 create ${eonwebdb}
mysqladmin -u root --password=root66 create ${nagiosbpdb}

# create the database content
mysql -u root --password=root66 ${eonwebdb} < ${eonconfdir}/eonweb.sql
mysql -u root --password=root66 ${nagiosbpdb} < ${eonconfdir}/nagiosbp.sql

# Change DocumentRoot for apache
sed -i 's/^DocumentRoot.*/DocumentRoot\ \"\/srv\/eyesofnetwork\/eonweb\"/g' /etc/httpd/conf/httpd.conf

# crons for eon
cp -rf ${eonconfdir}/eonbackup /etc/cron.d/
cp -rf ${eonconfdir}/eondowntime /etc/cron.d/
cp -rf ${eonconfdir}/eonwebpurge /etc/cron.d/

# start the services
/etc/init.d/httpd restart   > /dev/null 2>&1

