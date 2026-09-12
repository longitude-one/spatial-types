# Some tips to use quality tools

## Quick start

```bash
# install quality tools
composer upgrade-quality-tools
# install library-dependencies if not already done
composer update
# launch quality checks
composer quality
# launch test
composer test
# launch test with coverage information
composer test-local
```
 
In this repository we use PHP_CodeSniffer (phpcs), PHP CS Fixer, PHP Mess Detector (phpmd), and PHPStan to enforce and improve code quality: 

* **PHP_CodeSniffer** enforces the project's coding standards and flags style violations and suspicious constructs; 
* **PHP CS Fixer** automatically reformats source code to the configured style rules while preserving behavior; 
* **PHP Mess Detector** detects potential bugs, dead or unused code, and maintainability issues such as high cyclomatic complexity; 
* **PHPStan** performs advanced static analysis with type inference to surface type-related errors, incorrect API usage, and contract violations before runtime.

## How to regenerate PHP-Stan baseline

```bash
quality/php-stan/vendor/bin/phpstan analyse --configuration=quality/php-stan/php-stan.neon lib tests --error-format=table --no-progress --no-interaction --no-ansi --level=9 --memory-limit=256M --generate-baseline quality/php-stan/phpstan-baseline.neon
```
