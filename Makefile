#!/usr/bin/make

PHP_CONTAINER  = php
EXEC_PHP_FPM   = docker exec $(PHP_CONTAINER) sh -c
SHELL = /bin/bash

USER_ID := $(shell id -u)
GROUP_ID := $(shell id -g)
export USER_ID
export GROUP_ID

.PHONY: help

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

.DEFAULT_GOAL := help

## ----- Docker ------

build: ## Build containers
	docker-compose build

build-nc: ## Build containers without caching
	docker-compose build --no-cache

up: ## Start containers
	docker-compose up -d

down: ## Stop containers
	docker-compose down

sh: ## Run shell on php container
	docker exec -it $(PHP_CONTAINER) $(SHELL)

## ----- Composer -----

setup: composer.lock ## Run bin/setup on first start
	$(EXEC_PHP_FPM) 'composer setup'

install: composer.lock ## Install vendors according to the current composer.lock file
	$(EXEC_PHP_FPM) 'composer install'

update: composer.json ## Update vendors according to the current composer.json file
	$(EXEC_PHP_FPM) 'composer update'

test: ## Launch unit test for project
	$(EXEC_PHP_FPM) 'composer test'

phpcs: ## Linter
	$(EXEC_PHP_FPM) 'composer phpcs'
	$(EXEC_PHP_FPM) 'rm .phpcs.cache'

phpstan: ## Linter fix error
	$(EXEC_PHP_FPM) 'composer phpstan'

## ----- Symfony ------

cc: ## Clear cache
	$(EXEC_PHP_FPM) 'bin/console c:c'

run: ## Run a Symfony command
	$(EXEC_PHP_FPM) "bin/console $(filter-out $@,$(MAKECMDGOALS))"

## ----- Divers ------

purge: ## Purge cache and logs
	rm -rf var/cache/* var/logs/*

