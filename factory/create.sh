#!/bin/bash

if [ "$(id -u)" != "0" ]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

# For future use - the base version of OpenCart to apply QuickCommerce to
SHOP_VERSION="2.2.0.0"

rm -rf workspace/shop
#git clone --depth 1 -b ${SHOP_VERSION} https://github.com/opencart/opencart.git workspace/shop
# Clone the Quick Commerce application (quickcommerce-oc or QuickCommerce's version of OpenCart)
git clone https://github.com/bluecollardev/quickcommerce-oc.git . && \
# Go to the quickcommerce-react lib folder and pull the quickcommerce (PHP lib) submodule
cd vendor && git submodule add https://github.com/bluecollardev/quickcommerce.git quickcommerce && \
# Exit vendor dir go back to the root dir
cd ../

# Install frontend submodule and fetch the submodule files 
# TODO: A var for the theme and repo path!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
git submodule add https://github.com/bluecollardev/clients-phobulous-theme frontend
# Create build directory for frontend files, this is where webpack will put the static output files
mkdir -p upload/staging 
# React toggle display package needs to be removed...
git submodule update --init --recursive

# Delete any existing volumes, we're about to create new volumes
rm -rf volume-qc volume-db

docker-compose rm -f --all

# Delete previously created artifacts by copying empty ones
cp ../images/php-fpm/files.tar.gz ../images/php-fpm
cp ../images/maria-db/data.tar.gz ../images/maria-db

# Install shop
docker-compose build php-fpm maria-db
docker-compose run --service-ports php-fpm
docker-compose stop maria-db

# Cleanup mysql files
#rm -rf volume-db/ib_*

# Create and move artifacts
tar -cfvz files.tar.gz -C volume-qc .
tar -cfvz data.tar.gz -C volume-db .
chown $(stat -c '%U:%G' .) *.tar.gz
mv files.tar.gz ../images/php-fpm
mv data.tar.gz ../images/maria-db

# Delete volumes - leave this out for now while devving
#rm -rf volume-php volume-mysql

docker-compose rm -f --all
#rm -rf workspace/shop
