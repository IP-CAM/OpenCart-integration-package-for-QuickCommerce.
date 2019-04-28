FROM php:5.6-fpm-alpine

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip

# Add setup/start scripts
COPY wait_for_service.sh /wait_for_service.sh
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /*.sh

# Go to tmp workdir
RUN mkdir -p ../tmp && cd ../tmp
RUN git clone https://github.com/firebrandsolutions/quickcommerce-oc.git . && \
# Go to the quickcommerce-react lib folder and pull the quickcommerce (PHP lib) submodule
cd vendor && git submodule add https://github.com/firebrandsolutions/quickcommerce.git quickcommerce && \
# Exit vendor dir go back to the root dir
RUN cd ../

# Install frontend submodule and fetch the submodule files 
RUN git submodule add https://github.com/firebrandsolutions/clients-phobulous-theme frontend
# Create build directory for frontend files, this is where webpack will put the static output files
RUN mkdir -p upload/staging 
# React toggle display package needs to be removed...
RUN git submodule update --init --recursive
# Install frontend submodule's node packages
RUN apk add --update nodejs npm
RUN cd frontend && npm install --save react-toggle-display
# Build the frontend app, which will drop files into the Quick Commerce front-end template
RUN webpack --watch

# Copy application contents from workspace to publish dir
RUN cd ../

COPY tmp/* /var/www/
# Clean-up
RUN rm -rf tmp

# Copy Quick Commerce shop config
COPY update_shop_config.php /var/www/update_shop_config.php

RUN echo 'EXECUTE ENTRYPOINT.SH'
CMD ["/entrypoint.sh"]
#CMD ["php-fpm"] # Execute from entrypoint script

EXPOSE 9000