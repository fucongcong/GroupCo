FROM php:7.1-fpm

MAINTAINER fucongcong

RUN apt-get update -yqq
RUN apt-get install -yqq libpcre3-dev
RUN apt-get install -yqq libfreetype6-dev
RUN apt-get install -yqq libjpeg62-turbo-dev
RUN apt-get install -yqq libmcrypt-dev
RUN apt-get install -yqq libpng-dev
RUN apt-get install -yqq libicu-dev
RUN apt-get install -yqq git
RUN apt-get install -yqq python
RUN docker-php-ext-install gd
RUN docker-php-ext-install zip
RUN docker-php-ext-install intl
RUN apt-get install -yqq build-essential chrpath git-core libssl-dev libfontconfig1-dev libxft-dev
RUN apt-get install -yqq wget
RUN apt-get install -yqq zip
RUN wget https://github.com/redis/hiredis/archive/v0.13.3.zip \
    && unzip v0.13.3.zip \
    && cd hiredis-0.13.3 \
    && make && make install \
    && ldconfig \
    && cd .. \
    && rm -rf hiredis-0.13.3
RUN wget https://github.com/swoole/swoole-src/archive/v1.9.22.zip \
    && unzip v1.9.22.zip \
    && cd swoole-src-1.9.22 \
    && phpize \
    && ./configure --enable-async-redis \
    && make \
    && make install \
    && cd .. \
    && rm -rf swoole-src-1.9.22
RUN echo "extension=swoole.so" > /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini
RUN php -m

RUN wget http://pecl.php.net/get/redis-4.1.0.tgz \
    && tar xzf redis-4.1.0.tgz \
    && cd redis-4.1.0 \
    && phpize \
    && ./configure \
    && make \
    && make install \
    && cd .. \
    && rm -rf redis-4.1.0
RUN echo "extension=redis.so" > /usr/local/etc/php/conf.d/docker-php-ext-redis.ini
RUN php -m

RUN docker-php-ext-install pdo_mysql
RUN php -m
ADD . /GroupCo
WORKDIR /GroupCo
