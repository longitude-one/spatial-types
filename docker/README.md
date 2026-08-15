Docker
======

This directory is only used to help contributing developers.
It creates a Docker environment with PHP 8.4.

How to build image?
----------------------

To help maintainer, this library comes with a docker environment.
It builds an image of the minimum PHP version.
This version is compiled to contain all needed tools.

```sh
export APP_FOLDER="$PWD"
docker compose build
docker compose up -d
```

How to load dependencies?
-------------------------

Composer is already installed in the image.

```sh
docker compose exec si-php8 composer install
```

How to start test?
------------------

```sh
docker compose exec si-php8 composer test
```
