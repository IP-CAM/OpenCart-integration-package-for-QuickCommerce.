FROM php:5.6-fpm-alpine

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install mysqli && \
    docker-php-ext-install gd && \
    docker-php-ext-install zip

# Add setup/start scripts
COPY wait_for_service.sh /wait_for_service.sh
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /*.sh

# Install frontend submodule's node packages
RUN apk add --update nodejs npm
RUN cd frontend && npm install --save react-toggle-display
# Build the frontend app, which will drop files into the Quick Commerce front-end template
RUN webpack --watch

# Copy application contents from workspace to publish dir
RUN cd ../

COPY tmp/* /var/www/quickcommerce/
# Clean-up
RUN rm -rf tmp

# Copy Quick Commerce shop config
COPY update_shop_config.php /var/www/quickcommerce/update_shop_config.php

RUN echo 'EXECUTE ENTRYPOINT.SH'
CMD ["/entrypoint.sh"]
#CMD ["php-fpm"] # Execute from entrypoint script

EXPOSE 9000