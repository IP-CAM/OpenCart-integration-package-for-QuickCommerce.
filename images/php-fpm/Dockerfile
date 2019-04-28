FROM php:5.6-fpm-alpine

RUN apk --update --no-cache add \
        php5 \
        php5-bcmath \
        php5-dom \
        php5-ctype \
        php5-curl \
        php5-fpm \
        php5-gd \
        php5-iconv \
        php5-intl \
        php5-json \
        php5-mcrypt \
        php5-opcache \
        php5-openssl \
        php5-pdo \
        php5-pdo_mysql \
        php5-pdo_pgsql \
        php5-pdo_sqlite \
        php5-phar \
        php5-posix \
        php5-soap \
        php5-xml \
        php5-zip \
        php5-zlib \
    && rm -rf /var/cache/apk/*

RUN curl -sS "https://getcomposer.org/installer" | php
RUN mv composer.phar /usr/local/bin/composer

# Add setup/start scripts
COPY wait_for_service.sh /wait_for_service.sh
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /*.sh

# Add application contents
RUN ls -la
ADD files.tar.gz /var/www/
RUN echo "Files in www directory"
RUN ls -la /var/www/
COPY update_shop_config.php /var/www/update_shop_config.php

# Install frontend submodule's node packages
#RUN apk add --update nodejs npm
#RUN cd frontend && npm install --save react-toggle-display
# Build the frontend app, which will drop files into the Quick Commerce front-end template
#RUN webpack --watch

# Copy application contents from workspace to publish dir
#RUN cd ../

#COPY tmp/* /var/www/quickcommerce/
# Clean-up
#RUN rm -rf tmp


RUN echo 'EXECUTE ENTRYPOINT.SH'
CMD ["/entrypoint.sh"] # Execute from entrypoint script
#CMD ["php-fpm"]

#EXPOSE 9000