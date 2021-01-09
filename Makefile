-include .env

COMPOSE_PROJECT_NAME = 'telegram-quest'

DOCKER_COMPOSE ?= docker-compose
RUN_PHP ?= $(DOCKER_COMPOSE) run --rm --no-deps php
RUN_COMPOSER = $(RUN_PHP) composer

EXECUTE_DB ?= $(DOCKER_COMPOSE) exec postgres

all: docker-compose.override.yml symfony-cli-install composer-install up cc
.PHONY: all

#
# Setup
#
docker-compose.override.yml:
	cp -v docker-compose.override.yml.dist docker-compose.override.yml

envfile:
	$(RUN_PHP) cat .env.dist > .env
.PHONY: envfile

symfony-cli-install:
	$(RUN_PHP) wget https://get.symfony.com/cli/installer -O tmp/symfony
	$(RUN_PHP) chmod +x tmp/symfony
	$(RUN_PHP) tmp/symfony --install-dir=./
	$(RUN_PHP) rm tmp/symfony
.PHONY: symfony-cli-install

cc:
#	$(RUN_PHP) rm -rf \
#		data/cache/DoctrineModule/cache/* \
#		data/cache/DoctrineORMModule/Proxy/* \
#		data/cache/exchange-rates/*/* \
#		data/cache/jms/*/* \
#		data/cache/translation/*/* \
#		data/cache/autowire/*/* \
#		data/cache/annotations/symfony-validator/*.validatorcache.data
#	$(RUN_PHP) mkdir -p \
#		data/cache/DoctrineModule/cache/ \
#		data/cache/DoctrineORMModule/Proxy/ \
#		data/cache/exchange-rates/ \
#		data/cache/jms/ \
#		data/cache/translation/ \
#		data/cache/autowire/ \
#		data/cache/annotations/symfony-validator/
#	$(RUN_PHP) ./doctrine -q orm:clear-cache:metadata --flush
#	$(RUN_PHP) ./doctrine -q orm:clear-cache:query --flush
#	$(RUN_PHP) ./doctrine -q orm:clear-cache:result --flush
#	$(RUN_PHP) ./doctrine orm:generate-proxies
#	$(RUN_PHP) ./zf reinfi:di cache warmup
.PHONY: cc

check-requirements:
	$(RUN_PHP) ./symfony check:requirements
.PHONY: check-requirements

#
# Utils
#
remove-all-images:
	docker rmi $$(docker images -q) --force
.PHONY: remove-all-images

psql:
	$(EXECUTE_DB) psql -U $(DB_USER) -d $(DB_NAME)
.PHONY: psql

ssh:
	$(EXECUTE_PHP) sh
.PHONY: ssh

#
# Docker Compose
#
ps:
	$(DOCKER_COMPOSE) ps
.PHONY: ps

restart:
	$(DOCKER_COMPOSE) restart
.PHONY: restart

logs:
	$(DOCKER_COMPOSE) logs -f
.PHONY: logs

pull:
	$(DOCKER_COMPOSE) pull
.PHONY: pull

up:
	$(DOCKER_COMPOSE) up --remove-orphans -d
.PHONY: up

stop:
	$(DOCKER_COMPOSE) stop
.PHONY: stop

down:
	$(DOCKER_COMPOSE) down --remove-orphans
.PHONY: down

config:
	$(DOCKER_COMPOSE) config
.PHONY: config

#
# Database
#
db-build:
	$(DOCKER_COMPOSE) build --no-cache --pull --force-rm db
.PHONY: db-build

db-clean:
	$(DOCKER_COMPOSE) rm -sfv db
.PHONY: db-clean

db-recreate: db-clean
	$(DOCKER_COMPOSE) up -d db
.PHONY: db-recreate

db-export:
	$(eval DATE_TAG:=$(shell date +'%Y_%m_%d-%H%M%S'))
	$(EXECUTE_DB) pg_dump -n public -Fc -U $(DB_USER) -v -O -x -f /tmp/$(COMPOSE_PROJECT_NAME)_$(DATE_TAG).dump $(DB_NAME)
.PHONY: db-export

db-migrate:
	$(RUN_PHP) ./doctrine migration:migrate -n
.PHONY: db-migrate

db-prev:
	$(RUN_PHP) ./doctrine migration:migrate prev -n
.PHONY: db-prev

db-next:
	$(RUN_PHP) ./doctrine migration:migrate next -n
.PHONY: db-next

db-diff:
	$(RUN_PHP) ./doctrine -q orm:clear-cache:metadata --flush
	$(RUN_PHP) ./doctrine migration:diff -n
.PHONY: db-diff

db-gen:
	$(RUN_PHP) ./doctrine -q orm:clear-cache:metadata --flush
	$(RUN_PHP) ./doctrine migration:generate -n
.PHONY: db-gen

#
# Quality
#
cs: cs-fixer
.PHONY: cs

cs-fixer:
	$(RUN_COMPOSER) run-script cs-fixer
.PHONY: cs-fixer

#
# Composer
#
composer-update-lock:
	$(RUN_COMPOSER) update --lock
.PHONY: composer-update-lock

composer-install:
	#$(RUN_COMPOSER) install
.PHONY: composer-install

composer-update:
	$(RUN_COMPOSER) update $(package)
.PHONY: composer-update

composer-update-dev:
	$(RUN_COMPOSER) update --dev $(package)
.PHONY: composer-update-dev

composer-require:
	$(RUN_COMPOSER) require $(package)
.PHONY: composer-require

composer-require-dev:
	$(RUN_COMPOSER) require --dev $(package)
.PHONY: composer-require-dev

composer-remove:
	$(RUN_COMPOSER) remove $(package)
.PHONY: composer-remove

composer-remove-dev:
	$(RUN_COMPOSER) remove --dev $(package)
.PHONY: composer-remove-dev