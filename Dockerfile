FROM php:7.4-fpm-alpine

USER root

RUN apk add bash
RUN docker-php-ext-install calendar bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

CMD /usr/local/sbin/php-fpm

ENTRYPOINT ["bash", "run.sh", "init"]
