CONTAINER=composer-unused-7.4

up:
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

box:
	docker exec -it $(CONTAINER) php box.phar compile

ssh:
	docker exec -it $(CONTAINER) /bin/sh
