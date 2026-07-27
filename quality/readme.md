# Some tips to use quality tools

## Quick start

```bash
cd docker
# build composer image and run a container
docker compose build
docker compose up -d
# install quality tools
docker compose exec si-php8 composer install --working-dir=quality/php-cs-fixer
docker compose exec si-php8 composer install --working-dir=quality/php-stan
docker compose exec si-php8 composer install --working-dir=quality/php-mess-detector
docker compose exec si-php8 composer install --working-dir=quality/php-code-sniffer
# install library-dependencies if not already done
docker compose exec si-php8 composer install
# launch quality checks
docker compose exec si-php8 composer quality
```
 
In this repository we use PHP_CodeSniffer (phpcs), PHP CS Fixer, PHP Mess Detector (phpmd), and PHPStan to enforce and improve code quality: 

* **PHP_CodeSniffer** enforces the project's coding standards and flags style violations and suspicious constructs; 
* **PHP CS Fixer** automatically reformats source code to the configured style rules while preserving behavior; 
* **PHP Mess Detector** detects potential bugs, dead or unused code, and maintainability issues such as high cyclomatic complexity; 
* **PHPStan** performs advanced static analysis with type inference to surface type-related errors, incorrect API usage, and contract violations before runtime.

## How to regenerate PHP-Stan baseline

```bash
docker compose exec si-php8 quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M --generate-baseline quality/php-stan/phpstan-baseline.neon
```
