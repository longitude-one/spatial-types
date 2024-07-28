## Tests

The `Old` directory contains the tests of the doctrine-spatial extension to be avoid breaking compatibility.
The `Unit` directory contains the unit tests of the spatial types extension.

### Running the tests

To run the tests, you need to install the dependencies using composer:

```bash
$ composer update
```

Then, run the following command:

```bash
$ vendor/bin/phpunit
```

If you use the docker environment, you can run the following commands:

```bash
$ docker exec si-php81 composer update
$ docker exec si-php81 vendor/bin/phpunit
```