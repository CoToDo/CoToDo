FROM php:7.1-apache

WORKDIR /var/www/html/

#install additional libraries
RUN apt-get update
RUN apt-get install -y \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev

#install additional php extensions
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install -j$(nproc) zip

#use production php.ini configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ENV APP_ENV prod
ENV APP_DEBUG 0
ENV DATABASE_URL "sqlite:///%kernel.project_dir%/var/data.db"

#install compoer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" 
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"


COPY . /var/www/html/

#install symfony, dependencies, setup database
RUN /var/www/html/composer.phar install
RUN php bin/console doctrine:database:create
RUN php bin/console doctrine:migrations:migrate
RUN php bin/console cache:clear

#fix permissions
RUN chown -R www-data:www-data var/cache/
RUN chown -R www-data:www-data var/log/

#configure Apache
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN cp vhost.conf /etc/apache2/sites-available/cotodo.conf
RUN a2ensite cotodo.conf
RUN a2dissite 000-default.conf

EXPOSE 80

