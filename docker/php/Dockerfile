FROM php:8.1-fpm

RUN apt-get update && apt-get install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Install node
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash
RUN apt-get install --yes nodejs
RUN node -v
RUN npm -v

RUN chmod -R a+rwx /usr/bin \
  && chmod -R a+rwx /usr/lib/node_modules \
  && mkdir  /var/www/.npm \
  && chown -R www-data:www-data /var/www/.npm

# Install front reqs
RUN npm --loglevel=error install -g --unsafe-perm gulp-cli
RUN npm --loglevel=error install -g --unsafe-perm webpack webpack-cli

# Set the working directory to /var/www/html
WORKDIR /var/www/html