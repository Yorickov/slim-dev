install:
	composer install

start:
	php -S localhost:8000 -t public public/index.php

lint:
	composer run-script phpcs -- --standard=PSR12 public

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 public

reload:
	composer dump-autoload

test: lint
	composer run-script phpunit tests
