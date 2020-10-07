FROM php:7.4-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 80
WORKDIR /app

# git, unzip & zip are for composer
RUN apt-get update -qq && \
    apt-get install -qy \
    git \
    gnupg \
    unzip \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql
COPY docker/conf/php.conf /usr/local/etc/php/conf.d/app.ini

# Apache
COPY docker/conf/vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/conf/apache.conf /etc/apache2/conf-available/z-app.conf

# CovidApp : pas besoin de copier !
#COPY app /app/app
#COPY config /app/config
#COPY logs /app/logs
#COPY public /app/public
#COPY src /app/src
#COPY templates /app/templates
#COPY tests /app/tests
#COPY var /app/var
#COPY vendor /app/vendor

RUN a2enmod rewrite remoteip && \
    a2enconf z-app
