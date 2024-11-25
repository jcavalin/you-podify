FROM php:8.2-fpm


RUN apt-get update && \
    apt-get install -y git zip unzip

RUN pecl install xdebug-3.2.1 && \
	docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/src/myapp

EXPOSE 8080
