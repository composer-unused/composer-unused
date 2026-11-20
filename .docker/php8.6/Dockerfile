FROM php:8.6-alpine

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/bin/
RUN chmod +x /usr/bin/install-php-extensions
# pecl_http (raphf), ext-ds, Xdebug and memcached do not build/support PHP
# 8.6 yet (Xdebug's own package caps at <=8.5.99); tests needing the skipped
# extensions (@requires extension http/ds/memcached) are skipped on this
# image. php-ds/php-ds's "provide: ext-ds" satisfies the require-dev platform
# check without the real extension, until upstream releases add 8.6 support.
RUN install-php-extensions opcache zip mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /docker/composer-unused
