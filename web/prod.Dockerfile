FROM php:7.3-fpm
ARG WITH_XDEBUG
ARG APP_URL
ARG ASSET_URL
ARG DB_CONNECTION
ARG DB_HOST
ARG DB_PORT
ARG DB_DATABASE
ARG DB_USERNAME
ARG DB_PASSWORD
#AD CONFIG
ARG LDAP_HOST
ARG LDAP_USERNAME
ARG LDAP_PASSWORD
ARG LDAP_BASE_DN

ENV APP_URL=${APP_URL}
ENV DB_CONNECTION=${DB_CONNECTION}
ENV DB_HOST=${DB_HOST}
ENV DB_PORT=${DB_PORT}
ENV DB_DATABASE=${DB_DATABASE}
ENV DB_USERNAME=${DB_USERNAME}
ENV DB_PASSWORD=${DB_PASSWORD}
#ENV ASSET_URL=${ASSET_URL}
#AD CONFIG
ENV LDAP_HOST=${LDAP_HOST}
ENV LDAP_USERNAME=${LDAP_USERNAME}
ENV LDAP_PASSWORD=${LDAP_PASSWORD} 
ENV LDAP_BASE_DN=${LDAP_BASE_DN}
#install tools, image optimizers
RUN apt-get update && apt-get -y install bash && apt-get -y install zip nano \
    && apt-get -y install libmcrypt-dev jpegoptim optipng pngquant gifsicle webp iputils-ping libxml2-dev \
    && apt-get -y install libpng-dev libfreetype6-dev libjpeg62-turbo-dev cron libpq-dev libzip-dev

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
#####################################
# Install Java Jre
#####################################
RUN apt-get install -y software-properties-common
RUN apt-get update -yqq
#RUN mkdir /usr/share/man/man1 
RUN apt-add-repository 'deb http://security.debian.org/debian-security stretch/updates main'
RUN apt-get update
#RUN apt-get install -y openjdk-8-jdk
###################################
ENV ACCEPT_EULA=Y
#####################################
# Install extensions
#####################################
RUN docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install mbstring pdo pdo_mysql pdo_pgsql zip
#soap
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


RUN mkdir /application

COPY ./config/custom.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./src /application
COPY ./src/.env.prod /application/.env
RUN chown www-data:www-data -R /application
RUN chmod -R 777 /application/storage
RUN chmod -R 777 /application/bootstrap/cache/
WORKDIR /application

RUN composer install --no-scripts --no-autoloader --no-dev
RUN composer dump-autoload
RUN php artisan key:generate
RUN php artisan config:clear
RUN php artisan cache:clear