install:
	composer install

start:
	php -S localhost:8000 -t public public/index.php

lint:
	composer run-script phpcs -- --standard=PSR12 public src

lint-fix:
	composer run-script phpcbf -- --standard=PSR12 public src

reload:
	composer dump-autoload

test:
	composer run-script phpunit tests
