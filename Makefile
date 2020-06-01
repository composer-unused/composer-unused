up:
	if [ "$(shell docker-machine status default)" != 'Running' ]; then \
		docker-machine start default; \
	fi;
	eval ${docker-machine env}
	docker-compose up -d

down:
	docker-compose down

7.4:
	eval $(export COMPOSER_UNUSED_PHP_VERSION=composer-unused-7.4)

7.3:
	eval $(export COMPOSER_UNUSED_PHP_VERSION=composer-unused-7.3)

install:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) composer install

update:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) composer update

check: csfix cs phpunit analyse

phpunit:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) vendor/bin/phpunit

analyse:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) vendor/bin/phpstan analyse

cs:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) vendor/bin/phpcs

csfix:
	docker exec -it $(COMPOSER_UNUSED_PHP_VERSION) vendor/bin/phpcbf
