FROM php:7-apache
MAINTAINER Johannes Lauinger <johannes@lauinger-it.de>

RUN apt-get update && \
    apt-get install -y --no-install-recommends git zip unzip \
      imagemagick pdftk ghostscript poppler-utils

RUN curl --silent --show-error https://getcomposer.org/installer | \
    php -- --install-dir=/usr/local/bin --filename=composer

RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

COPY . /srv/app
WORKDIR /srv/app

RUN chown -R www-data:www-data .
RUN chmod -R g+w storage
RUN mkdir /docmanimport && chown -R www-data:www-data /docmanimport

RUN cp docker/docker.env .env
RUN rm .env.example
RUN cp docker/docker.htaccess public/.htaccess

RUN composer install
RUN php artisan key:generate

VOLUME ["/srv/app/storage/app/documents"]
VOLUME ["/docmanimport"]

ENTRYPOINT php artisan config:cache && exec apache2-foreground
