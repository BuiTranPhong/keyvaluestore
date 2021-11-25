FROM php:7.3-fpm
ARG WITH_XDEBUG

#install tools, image optimizers
RUN apt-get update && apt-get -y install git && apt-get -y install bash && apt-get -y install zip nano \
    && apt-get -y install libmcrypt-dev jpegoptim optipng pngquant gifsicle webp iputils-ping libxml2-dev \
    && apt-get -y install libpng-dev libfreetype6-dev libjpeg62-turbo-dev cron rsyslog libpq-dev libzip-dev

RUN yes | if [ $WITH_XDEBUG = "true" ] ; then \
        pecl install xdebug; \ 
        docker-php-ext-enable xdebug; \
        echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
        echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;  \
        echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \ 
        echo "xdebug.client_port=9001" >> /usr/local/etc/php/conf.d/xdebug.ini; \
        echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini; \
        echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/xdebug.ini; \
        echo "xdebug.idekey=VSCODE" >> /usr/local/etc/php/conf.d/xdebug.ini; \
    fi
#install composer

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install LDAP
RUN apt-get install -y libldap2-dev && \
    docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/ && \
    docker-php-ext-install ldap

ENV ACCEPT_EULA=Y
#####################################
# Install extensions
#####################################
RUN docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install mbstring pdo pdo_mysql pdo_pgsql zip
#####################################
# Install gd extension
#####################################
RUN docker-php-ext-configure gd --with-freetype-dir --with-jpeg-dir --with-png-dir \
    && docker-php-ext-install gd
#####################################
# Exif:
#####################################
RUN docker-php-ext-install exif \
    && docker-php-ext-enable exif
#####################################
# Imagemagick:
#####################################
RUN apt-get install -y libmagickwand-dev imagemagick && \
    pecl install imagick && \
    docker-php-ext-enable imagick

RUN mkdir /application && chown www-data:www-data -R /application

# Copy hello-cron file to the cron.d directory
USER root
# Give execution rights on the cron job
#RUN chmod 0644 /etc/cron.d/cron
#apply cron job
#RUN crontab /etc/cron.d/cron
#create the log file to be able to run tail
#RUN touch /var/log/cron.log
#run the command on container startup
COPY ./src/composer.json /application
#CMD cron && docker-php-entrypoint php-fpm
WORKDIR /application
