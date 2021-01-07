FROM php:7.4-fpm
MAINTAINER Adrien Chinour <git@chinour.fr>

ARG USER_ID
ARG GROUP_ID

RUN groupadd -f -g $GROUP_ID app \
    && useradd -u $USER_ID -g $GROUP_ID app

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nano \
    git \
    apt-transport-https \
    build-essential \
    apt-utils \
    openssl \
    gnupg \
    libpng-dev \
    locales \
    libicu-dev \
    zip \
    unzip \
    libzip-dev \
    sqlite3 \
    libsqlite3-dev; \
    \
    rm -rf /var/lib/apt/lists/*

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini; \
  sed -i -e "s/^ *memory_limit.*/memory_limit = 2G/g" \
  -e "s/^ *max_execution_time.*/max_execution_time = 0/g" \
  /usr/local/etc/php/php.ini

RUN docker-php-ext-configure intl; \
	docker-php-ext-install zip intl gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
ENV PATH="${PATH}:/root/.composer/vendor/bin"

RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

USER app

WORKDIR /app
EXPOSE 9000
CMD ["php-fpm"]
