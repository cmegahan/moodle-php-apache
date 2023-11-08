#!/usr/bin/env bash

set -e

echo "Installing apt dependencies"

# Build packages will be added during the build, but will be removed at the end.
BUILD_PACKAGES="gettext libcurl4-openssl-dev libfreetype6-dev libicu-dev libjpeg62-turbo-dev \
  libmariadb-dev libpng-dev libpq-dev libxml2-dev libxslt-dev \
  uuid-dev"

# Packages for MariaDB and MySQL.
PACKAGES_MYMARIA="libmariadb3"

# Packages for other Moodle runtime dependenices.
PACKAGES_RUNTIME="ghostscript libaio1 libcurl4 libgss3 libicu72 libmcrypt-dev libxml2 libxslt1.1 \
  libzip-dev sassc unzip zip"

apt-get update
apt-get install -y --no-install-recommends apt-transport-https \
    $BUILD_PACKAGES \
    $PACKAGES_MYMARIA \
    $PACKAGES_RUNTIME \

echo "Installing php extensions"

# ZIP
docker-php-ext-configure zip --with-zip
docker-php-ext-install zip

docker-php-ext-install -j$(nproc) \
    exif \
    intl \
    mysqli \
    opcache \
    soap \
    xsl

# GD.
docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/
docker-php-ext-install -j$(nproc) gd

# APCu, igbinary, Memcached, MongoDB, PCov, Redis, Solr, timezonedb, uuid, XMLRPC (beta)
pecl install igbinary redis timezonedb uuid xmlrpc-beta
docker-php-ext-enable igbinary redis timezonedb uuid xmlrpc

# Keep our image size down..
pecl clear-cache
apt-get remove --purge -y $BUILD_PACKAGES
apt-get autoremove -y
apt-get clean
rm -rf /var/lib/apt/lists/*