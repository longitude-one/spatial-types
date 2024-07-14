Docker
======

This directory is only used to help the contributing developers. 
It creates a docker environment with PHP 8.1. 

How to build image?
----------------------
To help maintainer, this library comes with a docker environment.
It builds an image of the minimum PHP version.
This version is compiled to contain all needed tools.
```sh
cd docker
docker compose build
docker compose up -d
```

How to load dependencies?
-------------------------
Composer is already installed in the image.
```
docker exec si-php8 composer update
```

How to start test?
------------------
```sh 
docker run spatial-php8 composer test-coverage
```

