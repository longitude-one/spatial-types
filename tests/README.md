# Tests

The `Unit` directory contains the unit tests of the spatial types extension.
The `Functional` directory contains functional tests.

## Running the tests

To run the tests, you need to install the dependencies using composer:

```bash
$ composer install
```

Then, run the following command:

```bash
$ composer test
```

### Symfony Validator compatibility

CI runs the complete `composer test` suite with Symfony Validator 5.4.x,
6.4.x, 7.4.x and 8.1.x on PHP 8.4, in addition to the existing PHP 8.4 and
8.5 jobs. Each compatibility job forces its selected minor version and verifies
the installed version before running the tests.

To reproduce a compatibility run locally, use a temporary Composer constraint
(replace `5.4.*` with `6.4.*`, `7.4.*` or `8.1.*` for the other versions):

```bash
composer update --with 'symfony/validator:5.4.*' --prefer-dist --no-interaction
composer show symfony/validator
composer test
```

This does not change the supported range in `composer.json`. Run `composer update`
without the temporary constraint to return to the default dependency resolution.
