#!/bin/bash
# QuickCommerce Installer
# v 0.0.1 Lucas Lopatka 
# GitHub @firebrandsolutions
# TODO: I'm just documenting the procedure here
# Final version will be in 1) PHP + 2) Node

echo "Please enter the ABSOLUTE PATH to the installation directory! (example: /home/bobjohnson/public_html/)"
read installdir
echo "Please enter a FOLDER NAME for your installation! (example: quickcommerce_oc)"
read dirname
echo "Please enter the URI to your theme's git repo! (example: https://github.com/firebrandsolutions/clients-phobulous-theme)"
read themerepo
libname=quickcommerce
 
cd ${installdir}
mkdir -p -- ${dirname}
cd ${dirname}

installpath=${installdir}/${dirname}

git clone https://github.com/firebrandsolutions/quickcommerce-oc.git . && # Clone QuickCommerce for OpenCart launcher
cd vendor && 
#git clone https://github.com/firebrandsolutions/quickcommerce.git ${libname} && # Clone QuickCommerce core libs
git submodule add https://github.com/firebrandsolutions/quickcommerce.git ${libname} && # Clone QuickCommerce core libs
cd ../ && # Back to root directory
# Clone theme into frontend dir
git clone themerepo frontend && # ie: https://github.com/firebrandsolutions/clients-phobulous-theme

# If /root/.my.cnf exists then it won't ask for root password
if [ -f /root/.my.cnf ]; then
	echo "Please enter the NAME of the new database! (example: database1)"
	read dbname
	echo "Please enter the database CHARACTER SET! (example: latin1, utf8, ...)"
	read charset
	echo "Creating new database..."
	mysql -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	mysql -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new database user!"
	read userpass
	echo "Creating new user..."
	mysql -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	mysql -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	mysql -e "FLUSH PRIVILEGES;"
    
    echo "Moving back to root directory."
    cd ${installdir}
    ls -l
    
    echo "Installing database..."
    mysql -u ${username} -p ${userpass} ${dbname} < qcdb-default.sql
	
    echo "You're good now :)"
	#exit
	
# If /root/.my.cnf doesn't exist then it'll ask for root password	
else
	echo "Please enter root user MySQL password!"
	read rootpasswd
	echo "Please enter the NAME of the new database! (example: database1)"
	read dbname
	echo "Please enter the database CHARACTER SET! (example: latin1, utf8, ...)"
	read charset
	echo "Creating new database..."
	mysql -uroot -p ${rootpasswd} -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET ${charset} */;"
	echo "Database successfully created!"
	echo "Showing existing databases..."
	mysql -uroot -p ${rootpasswd} -e "show databases;"
	echo ""
	echo "Please enter the NAME of the new database user! (example: user1)"
	read username
	echo "Please enter the PASSWORD for the new database user!"
	read userpass
	echo "Creating new user..."
	mysql -uroot -p ${rootpasswd} -e "CREATE USER ${username}@localhost IDENTIFIED BY '${userpass}';"
	echo "User successfully created!"
	echo ""
	echo "Granting ALL privileges on ${dbname} to ${username}!"
	mysql -uroot -p ${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${username}'@'localhost';"
	mysql -uroot -p ${rootpasswd} -e "FLUSH PRIVILEGES;"
    
    echo "Moving back to root directory."
    cd ${installdir}
    ls -l
	
    echo "Installing database..."
    mysql -uroot -p ${rootpasswd} ${dbname} < qcdb-default.sql
    
    echo "You're good now :)"
	#exit
fi

# Replace vars in config file
# Define the variables with values you want replaced
echo "Please enter the site URL! (example: mysite.com)"
read siteurl

config=catalog-config.php 
while read -r line
do 
done < ${config} 

${installpath}/upload/config.php

# Replace front controller since we use Journal2 modules (for now anyway)
# Journal2 modules by default only work with Journal2 theme
# replace front.php
cd upload/system/engine &&
mv front.php front.off.php &&
mv front.patched.php front.php

# Now need to build modifications
# They should be stored in DB if using the project launcher

# Create system/modification folder if it doesn't exist
# TODO: Define 'QUICKCOMMERCE_INSTALLED' in config.php or we break