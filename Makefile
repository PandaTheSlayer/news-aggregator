PHP=docker-compose exec php

phpshell php: args=bash
phpshell php: ## Enter php shell
	$(PHP) $(args)
.PHONY: phpshell

###################
# Composer targets #
###################
composer-%: CMD=$*
composer-%: ## Run composer command (Example: make composer-req args="rector/rectory=dev-main --dev")
	@$(PHP) php -dmemory_limit=-1 /usr/local/bin/composer $(CMD) $(args)
.PHONY: composer-%

sf-%: CMD=$*
sf-%: ## Run Symfony command (Example: make sf-cache:clear args="-e dev")
	$(PHP) bin/console $(CMD) $(args)
.PHONY: sf-%

psalm-check: ## Just analyze PHP code with psalm
	$(PHP) php -dmemory_limit=-1 vendor/bin/psalm --show-info=false $(args)
.PHONY: psalm-check

psalm-check-all: ## Just analyze PHP code with psalm
	$(PHP) php -dmemory_limit=-1 vendor/bin/psalm --show-info=true $(args)
.PHONY: psalm-check-all