PHPSPEC=vendor/bin/phpspec
BEHAT=vendor/bin/behat
README_TESTER=vendor/bin/readme-tester
PHPSTAN=vendor/bin/phpstan
PHPCS=vendor/bin/phpcs

COMPOSER_CMD=composer

.DEFAULT_GOAL=all

.PHONY: all
all: test docs analyze

.PHONY: clean
clean:
	rm composer.lock
	rm -rf vendor
	rm -rf vendor-bin

.PHONY: test
test: phpspec behat

.PHONY: phpspec
phpspec: vendor-bin/installed
	$(PHPSPEC) run

.PHONY: behat
behat: vendor-bin/installed
	$(BEHAT) --stop-on-failure

.PHONY: docs
docs: vendor-bin/installed
	$(README_TESTER) README.md

.PHONY: analyze
analyze: phpstan phpcs

.PHONY: phpstan
phpstan: vendor-bin/installed
	$(PHPSTAN) analyze -c phpstan.neon -l 7 src

.PHONY: phpcs
phpcs: vendor-bin/installed
	$(PHPCS) src --standard=PSR2 --ignore=router_template.php
	$(PHPCS) spec --standard=spec/ruleset.xml

composer.lock: composer.json
	@echo composer.lock is not up to date

vendor/installed: composer.lock
	$(COMPOSER_CMD) install
	touch $@

vendor-bin/installed: vendor/installed
	$(COMPOSER_CMD) bin phpspec require phpspec/phpspec:">=5"
	$(COMPOSER_CMD) bin behat require behat/behat:^3
	$(COMPOSER_CMD) bin readme-tester require hanneskod/readme-tester:^1.0@beta
	$(COMPOSER_CMD) bin phpstan require "phpstan/phpstan:<2"
	$(COMPOSER_CMD) bin phpcs require squizlabs/php_codesniffer:^3
	touch $@
