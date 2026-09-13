## Tests

The `Unit` directory contains the unit tests of the spatial types extension.
The `Functional` directory contains functional tests.

### Running the tests

To run the tests, you need to install the dependencies using composer:

```bash
$ composer install
```

Then, run the following command:

```bash
$ composer test
```

If you use the docker environment, you can run the following commands:

```bash
$ docker compose exec si-php8 composer install
$ docker compose exec si-php8 composer test
```
