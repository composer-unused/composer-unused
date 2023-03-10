PHP_VERSION=7.4

up: ## Run all containers in all versions
	docker compose up -d

down: ## Shut down all containers
	docker compose down --remove-orphans

clean: ## Clean vendor/ and composer.lock
	rm -rf vendor/ composer.lock

install: ## Run `composer install`
	docker compose run php$(PHP_VERSION) composer install $(filter-out $@, $(MAKECMDGOALS))

update: ## Run `composer update`
	docker compose run php$(PHP_VERSION) composer update $(filter-out $@, $(MAKECMDGOALS))

require: ## Run `composer require`
	docker compose run php$(PHP_VERSION) composer require $(filter-out $@, $(MAKECMDGOALS))

remove: ## Run `composer remove`
	docker compose run php$(PHP_VERSION) composer remove $(filter-out $@, $(MAKECMDGOALS))

check: cs analyse phpunit ## Run all checks in succession (phpcs, phpunit, phpstan)

phpunit: ## Run phpunit tests
	docker compose run php$(PHP_VERSION) vendor/bin/phpunit

analyse: ## Run phpstan analyse
	docker compose run php$(PHP_VERSION) vendor/bin/phpstan

cs: ## Run php cs
	docker compose run php$(PHP_VERSION) vendor/bin/phpcs

csfix: ## Run phpcs fixer
	docker compose run php$(PHP_VERSION) vendor/bin/phpcbf

box: ## Compile /build/composer-unused.phar
	docker compose run php$(PHP_VERSION) vendor/bin/box compile --no-parallel

ssh: ## SSH into container
	docker compose run php$(PHP_VERSION) /bin/sh

help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

%:
	@:
