####################################################
# Castopod development Docker file
####################################################
# ⚠️ NOT optimized for production
# should be used only for development purposes
#---------------------------------------------------
FROM php:8.1-fpm

LABEL maintainer="Yassine Doghri <yassine@doghri.fr>"

COPY . /castopod
WORKDIR /castopod

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/production/app/entrypoint.sh /entrypoint.sh

COPY docker/production/app/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
RUN \
    # install composer
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer \
    # install ci requirements
    && apk add --no-cache \
        nodejs \
        # install npm for @semantic-release/npm
        npm \
        git \
        unzip \
        wget \
        jq \
        zip \
        openssh-client \
        rsync \
        icu-libs \
        mysql \
        mysql-client \
    && apk add --no-cache --virtual .php-ext-build-dep icu-dev \
    && docker-php-ext-install \
        intl \
        mysqli \
    && apk del .php-ext-build-dep \
    # install pnpm
    && wget -qO- https://get.pnpm.io/install.sh | ENV="~/.shrc" SHELL="$(which sh)" sh - \
    && mv ~/.local/share/pnpm/pnpm /usr/bin/pnpm \
    && rm -rf ~/.local \
    # set pnpm store directory
    && pnpm config set store-dir .pnpm-store \
    # set composer cache directory
    && composer config -g cache-dir .composer-cache
# Install server requirements
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get update \
    && apt-get install --yes --no-install-recommends nodejs \
    # gnupg to sign commits with gpg
    gnupg \
    openssh-client \
    # cron for scheduled tasks
    cron \
    # unzip used by composer
    unzip \
    # required libraries to install php extensions using
    # https://github.com/mlocati/docker-php-extension-installer (included in php's docker image)
    libicu-dev \
    libpng-dev \
    libwebp-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libzip-dev \
    # ffmpeg for video encoding
    ffmpeg \
    # intl for Internationalization
    && docker-php-ext-install intl  \
    && docker-php-ext-install zip \
    # gd for image processing
    && docker-php-ext-configure gd --with-webp --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && docker-php-ext-install exif \
    && docker-php-ext-enable exif \
    # redis extension for cache
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable redis \
    # mysqli for database access
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    # configure php
    && echo "file_uploads = On\n" \
         "memory_limit = 512M\n" \
         "upload_max_filesize = 500M\n" \
         "post_max_size = 512M\n" \
         "max_execution_time = 300\n" \
         > /usr/local/etc/php/conf.d/uploads.ini

RUN apk add --no-cache libpng icu-libs freetype libwebp libjpeg-turbo libxpm ffmpeg && \
    apk add --no-cache --virtual .php-ext-build-dep freetype-dev libpng-dev libjpeg-turbo-dev libwebp-dev zlib-dev libxpm-dev icu-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm && \
    docker-php-ext-install gd intl mysqli exif && \
    docker-php-ext-enable mysqli gd intl exif && \
    apk del .php-ext-build-dep

COPY castopod /opt/castopod

RUN chmod 544 /entrypoint.sh && \
    chmod 444 /crontab.txt && \
    /usr/bin/crontab /crontab.txt

WORKDIR /opt/castopod

VOLUME /opt/castopod/public/media

EXPOSE 9000

ENTRYPOINT [ "sh", "-c" ]

CMD [ "/entrypoint.sh" ]
