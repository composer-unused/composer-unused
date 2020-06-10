CONTAINER=composer-unused-7.4

up:
	if [ "$(shell docker-machine status default)" != 'Running' ]; then \
		docker-machine start default; \
	fi;
	eval ${shell docker-machine env}
	docker-compose up -d

down:
	docker-compose down

install:
	docker exec -it $(CONTAINER) composer install

update:
	docker exec -it $(CONTAINER) composer update

check: csfix cs phpunit analyse

phpunit:
	docker exec -it $(CONTAINER) vendor/bin/phpunit

analyse:
	docker exec -it $(CONTAINER) vendor/bin/phpstan analyse

cs:
	docker exec -it $(CONTAINER) vendor/bin/phpcs

csfix:
	docker exec -it $(CONTAINER) vendor/bin/phpcbf
