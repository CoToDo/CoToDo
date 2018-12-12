FROM ubuntu:17.10

ENV APP_ENV dev

RUN apt-get update
RUN apt-get install -y --no-install-recommends \
	nginx \
	php7.1-fpm \
	apt-utils \
    ca-certificates \
    curl \
    unzip \
    git 

RUN apt-get install -y --no-install-recommends \
    php7.1-bcmath \
    php7.1-cli \
    php7.1-common \
    php7.1-curl \
    php7.1-gd \
    php7.1-imagick \
    php7.1-intl \
    php7.1-json \
    php7.1-mbstring \
    php7.1-mcrypt \
    php7.1-mysql \
    php7.1-sqlite3\
    php7.1-readline \
    php7.1-xml \
    php7.1-xmlrpc \
    php7.1-zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" 
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '93b54496392c062774670ac18b134c3b3a95e5a5e5c8f1a9f115f203b75bf9a129d5daa8ba6a13e2cc8a1da0806388a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"


WORKDIR /var/www

#CMD ["/usr/sbin/php-fpm7.1"]

COPY . /var/www

RUN //composer.phar install
RUN ls -la
RUN ls -la var
RUN cat .env
RUN php bin/console doctrine:database:create
RUN php bin/console doctrine:migrations:migrate

EXPOSE 8000


CMD ["php", "bin/console", "server:run"]
