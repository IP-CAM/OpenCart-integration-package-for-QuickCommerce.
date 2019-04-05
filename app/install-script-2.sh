#!/bin/bash
# QuickCommerce Installer
# v 0.0.1 Lucas Lopatka 
# GitHub @firebrandsolutions

echo "Please enter the ABSOLUTE PATH to the installation directory! (example: /home/bobjohnson/public_html/)"
read installdir
echo "Please enter a FOLDER NAME for your installation! (example: quickcommerce_oc)"
read dirname
 
cd ${installdir}
mkdir -p -- ${dirname}
cd ${dirname}

installpath=${installdir}/${dirname}
echo "Installation path: ${installpath}"

# If /root/.my.cnf exists then it won't ask for root password
if [ -f /root/.my.cnf ]; then
	echo "Please enter the NAME of the new database! (example: database1)"
	read dbname
	echo "Please enter the database CHARACTER SET! (example: latin1, utf8, ...)"
	read charset
	echo "Please enter the NAME of the new database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new database user!"
	read userpass
# If /root/.my.cnf doesn't exist then it'll ask for root password	
else
	echo "Please enter root user MySQL password!"
	read rootpasswd
	echo "Please enter the NAME of the new database! (example: database1)"
	read dbname
	echo "Please enter the database CHARACTER SET! (example: latin1, utf8, ...)"
	read charset
	echo "Please enter the NAME of the new database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new database user!"
	read userpass
fi

# Define the variables with values you want replaced
echo "Please enter the site URL! (example: mysite.com)"
read siteurl

cat catalog-config.php > ${installpath}/upload/config.php