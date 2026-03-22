DOCKER_COMPOSE = docker compose
PHP_CONTAINER = $(DOCKER_COMPOSE) exec php
CONSOLE = $(PHP_CONTAINER) php bin/console

.PHONY: help up down build bash test phpstan ecs ecs-fix db-create db-test-create jwt-keys cache-clear logs

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

## ————— Docker ——————————————————————————————————————————————————

up: ## Start all containers
	$(DOCKER_COMPOSE) up -d

down: ## Stop all containers
	$(DOCKER_COMPOSE) down

build: ## Build Docker images
	$(DOCKER_COMPOSE) build --no-cache

bash: ## Open a shell in the PHP container
	$(PHP_CONTAINER) bash

logs: ## Show container logs
	$(DOCKER_COMPOSE) logs -f

## ————— Application ——————————————————————————————————————————————

install: ## Install dependencies
	$(PHP_CONTAINER) composer install --no-interaction

cache-clear: ## Clear Symfony cache
	$(CONSOLE) cache:clear

## ————— Database ——————————————————————————————————————————————————

db-create: ## Create database schema via init.sql
	$(DOCKER_COMPOSE) exec database psql -U app_user -d app_db -f /docker-entrypoint-initdb.d/init.sql

db-test-create: ## Create test database and schema
	$(DOCKER_COMPOSE) exec database psql -U app_user -d postgres -c "SELECT 1 FROM pg_database WHERE datname = 'app_db_test'" | grep -q 1 || $(DOCKER_COMPOSE) exec database psql -U app_user -d postgres -c "CREATE DATABASE app_db_test"
	$(DOCKER_COMPOSE) exec database psql -U app_user -d app_db_test -f /docker-entrypoint-initdb.d/init.sql

db-reset: ## Drop and recreate database schema
	$(DOCKER_COMPOSE) exec database psql -U app_user -d app_db -c "DROP TABLE IF EXISTS users CASCADE"
	$(MAKE) db-create

## ————— Testing ——————————————————————————————————————————————————

test: ## Run all tests
	$(PHP_CONTAINER) php bin/phpunit

test-unit: ## Run unit tests
	$(PHP_CONTAINER) php bin/phpunit --testsuite=unit

test-integration: ## Run integration tests
	$(PHP_CONTAINER) php bin/phpunit --testsuite=integration

test-functional: ## Run functional tests
	$(PHP_CONTAINER) php bin/phpunit --testsuite=functional

test-coverage: ## Run tests with coverage report
	$(PHP_CONTAINER) php -d xdebug.mode=coverage bin/phpunit --coverage-html app/var/coverage

## ————— Code Quality ——————————————————————————————————————————————

phpstan: ## Run PHPStan static analysis
	$(PHP_CONTAINER) vendor/bin/phpstan analyse

ecs: ## Run Easy Coding Standard (dry run)
	$(PHP_CONTAINER) vendor/bin/ecs check

ecs-fix: ## Fix code style issues
	$(PHP_CONTAINER) vendor/bin/ecs check --fix

quality: ## Run all quality checks
	$(MAKE) phpstan
	$(MAKE) ecs
	$(MAKE) test

## ————— Security ——————————————————————————————————————————————————

jwt-keys: ## Generate JWT key pair
	$(PHP_CONTAINER) php bin/console lexik:jwt:generate-keypair --overwrite

## ————— Setup ——————————————————————————————————————————————————

setup: build up install db-create db-test-create jwt-keys ## Full project setup
	@echo "Project is ready at http://localhost:8080"
	@echo "API docs at http://localhost:8080/api/doc"
	@echo "Mailpit UI at http://localhost:8025"
