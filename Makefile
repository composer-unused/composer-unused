CONTAINER=composer-unused-7.4

up:
	docker-compose up -d

down:
	docker-compose down

clean:
	rm -rf vendor/ composer.lock

install:
	docker exec -it $(CONTAINER) composer install

update:
	docker exec -it $(CONTAINER) composer update $(filter-out $@, $(MAKECMDGOALS))

require:
	docker exec -it $(CONTAINER) composer require $(filter-out $@, $(MAKECMDGOALS))

remove:
	docker exec -it $(CONTAINER) composer remove $(filter-out $@, $(MAKECMDGOALS))

check: csfix cs phpunit analyse

phpunit:
	docker exec -it $(CONTAINER) vendor/bin/phpunit

analyse:
	docker exec -it $(CONTAINER) vendor/bin/phpstan analyse

cs:
	docker exec -it $(CONTAINER) vendor/bin/phpcs

csfix:
	docker exec -it $(CONTAINER) vendor/bin/phpcbf

box:
	docker exec -it $(CONTAINER) php box.phar compile

ssh:
	docker exec -it $(CONTAINER) /bin/sh

%:
	@true
