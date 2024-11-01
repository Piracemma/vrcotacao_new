FROM php:5.4-apache

RUN cp /etc/apt/sources.list /etc/apt/sources.list.old && \
    cat /dev/null > /etc/apt/sources.list

# Fix the source.list for jessie
RUN printf "deb http://archive.debian.org/debian/ jessie main\n" > /etc/apt/sources.list && \
    printf "deb-src http://archive.debian.org/debian/ jessie main\n" >>  /etc/apt/sources.list && \
    printf "deb http://archive.debian.org/debian-security jessie/updates main\n" >>  /etc/apt/sources.list && \
    printf "deb-src http://archive.debian.org/debian-security jessie/updates main" >>  /etc/apt/sources.list

RUN apt-get -y --allow-unauthenticated update && apt-get upgrade -y --allow-unauthenticated

RUN apt-get install -y --allow-unauthenticated libpq-dev
RUN apt-get install -y --allow-unauthenticated git
RUN apt-get install -y --allow-unauthenticated zip unzip
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN sed -i 's/short_open_tag = Off/short_open_tag = On/g' /usr/src/php/php.ini-production
RUN sed -i 's/display_errors = On/display_errors = Off/g' /usr/src/php/php.ini-production

RUN cp /usr/src/php/php.ini-production /usr/local/etc/php/php.ini

WORKDIR /var/www/html/cotacao

CMD ["apache2-foreground"]
