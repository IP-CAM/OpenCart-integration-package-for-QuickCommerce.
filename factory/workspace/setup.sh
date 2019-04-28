#!/bin/bash

find /var/www/quickcommerce/ -mindepth 1 -delete
# We only need to serve the upload dir
cp -r /tmp/workspace/quickcommerce/upload/. /var/www/quickcommerce

# TODO: These two lines are for Apache... 
#apache2-foreground > /dev/null 2>&1 &

# TODO: This line isn't right?!
/wait_for_service.sh ${MYSQL_HOST} 3306
# Install via CLI
# TODO: Modify cli_install script to use quickcommerce source DB
php /var/www/quickcommerce/install/cli_install.php install --db_hostname ${MYSQL_HOST} \
                               --db_username ${MYSQL_USER} \
                               --db_password ${MYSQL_PASSWORD} \
                               --db_database ${MYSQL_DATABASE} \
                               --db_driver mysqli \
                               --username ${SHOP_ADMIN_USER} \
                               --password ${SHOP_ADMIN_PASSWORD} \
                               --email ${SHOP_ADMIN_EMAIL} \
                               --http_server http://${VIRTUAL_HOST}/

rm -rf $(find /var/www/quickcommerce -name ".git" -or -name ".gitignore")
rm -rf /var/www/quickcommerce/install

# TODO: These two lines are for Apache... 
#mv /var/www/quickcommerce/.htaccess.txt /var/www/quickcommerce/.htaccess
#chown -R www-data:www-data /var/www/quickcommerce # Nginx equiv?