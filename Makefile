CFLAGS=-g
export CFLAGS

help: ## Show this help message.
	@echo 'usage: make [target] ...'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:(.+)?\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

down:
	docker compose down --remove-orphans

up:
	docker compose up -d

reset: down up ## Checks for code style issues
	docker compose run --rm php composer install
	docker compose run --rm php bin/console doctrine:database:drop -f
	docker exec -it windmill bin/console doctrine:database:create
	docker compose run --rm php bin/console doctrine:migrations:migrate first -n

windmill:
	@[ -n "$(COMMAND)" ] || { echo 'You must supply a command in the form of `make windmill COMMAND=play`'; exit 1; }
	docker compose run --rm php bin/console windmill:$(COMMAND)

cs: ## Checks for code style issues
	docker compose run --rm php bin/php-cs-fixer check

cs-fix: ## Fixes code style issues
	docker compose run --rm php bin/php-cs-fixer fix

test: ## Runs all tests
	docker compose run --rm php bin/phpunit
