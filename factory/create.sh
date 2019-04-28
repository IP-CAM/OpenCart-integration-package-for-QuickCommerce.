#!/bin/bash

#if [ "$(id -u)" != "0" ]; then
#   echo "This script must be run as root" 1>&2
#   exit 1
#fi

# For future use - the base version of OpenCart to apply QuickCommerce to
SHOP_VERSION="2.3.0.2"
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
IMAGES_PATH="${DIR}/../images"
WS_PATH="${DIR}/workspace"
OC_PATH="${WS_PATH}/quickcommerce"
VENDOR_PATH="${OC_PATH}/vendor"
QK_PKG_NAME="quickcommerce"
QK_LIB_PATH="${VENDOR_PATH}/${QK_PKG_NAME}"
FRONTEND_PKG_NAME="frontend"
FRONTEND_PATH="${OC_PATH}/${FRONTEND_PKG_NAME}"

rm -rf ${OC_PATH}
# Clone a fresh copy of OpenCart
echo "Cloning into directory ${OC_PATH}"
git clone --depth 1 -b ${SHOP_VERSION} https://github.com/opencart/opencart.git ${OC_PATH}
chown ${USER:=$(/usr/bin/id -run)} ${QC_PATH}
#chown ${USER:=$(/usr/bin/id -run)}:$USER ${DIR}/workspace/quickcommerce
echo "Install composer packages"
cd ${OC_PATH}
# Install composer packages
composer install

# Go to the vendor folder and pull in the quickcommerce (PHP lib) submodule
cd ${VENDOR_PATH}

git clone https://github.com/bluecollardev/quickcommerce.git ${QK_PKG_NAME}
#submodule add https://github.com/bluecollardev/quickcommerce.git quickcommerce
# Exit vendor dir go back to the root (quickcommerce) dir
cd ${OC_PATH}

# Install frontend submodule and fetch the submodule files 
# TODO: A var for the theme and repo path!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
git clone https://github.com/bluecollardev/clients-phobulous-theme ${FRONTEND_PKG_NAME}
#git submodule add https://github.com/bluecollardev/clients-phobulous-theme frontend
# Create build directory for frontend files, this is where webpack will put the static output files
mkdir -p ${OC_PATH}/upload/staging
# React toggle display package needs to be removed...
# Not used right now, as I'm cloning to get around some issues
#git submodule update --init --recursive

# Delete any existing volumes, we're about to create new volumes
rm -rf ${DIR}/volume-qc ${DIR}/volume-db

docker-compose rm -f --all

# Delete previously created artifacts by copying empty ones
cp ${IMAGES_PATH}/php-fpm/default-files.tar.gz ${IMAGES_PATH}/php-fpm/files.tar.gz
cp ${IMAGES_PATH}/maria-db/default-data.tar.gz ${IMAGES_PATH}/maria-db/data.tar.gz

# Build QuickCommerce
docker-compose build php-fpm maria-db
docker-compose run --service-ports php-fpm
docker-compose stop maria-db

# Cleanup mysql files
rm -rf ${DIR}/volume-db/ib_*

# Create and move artifacts
tar cfvz files.tar.gz -C ${DIR}/volume-qc .
tar cfvz data.tar.gz -C ${DIR}/volume-db .
#chown ${USER:=$(/usr/bin/id -run)} ${QC_PATH}
#chown $(stat -c '%U:%G' .) *.tar.gz
mv ${DIR}/files.tar.gz ${IMAGES_PATH}/php-fpm
mv ${DIR}/data.tar.gz ${IMAGES_PATH}/maria-db

# Delete volumes - leave this out for now while devving
#rm -rf volume-qc volume-db

docker-compose rm -f --all
rm -rf ${WS_PATH}

# Set cwd to original dir
cd ${DIR}

