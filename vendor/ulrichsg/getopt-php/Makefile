# vim: tabstop=4:softtabstop=4:shiftwidth=4:noexpandtab

help:
	@echo "Usual targets:"
	@echo "  test - run test suites"
	@echo ""
	@echo "Other targets:"
	@echo "  install-composer - install composer"
	@echo "  install-dependencies - install/update all vendor libraries using composer"
	@echo "  install-dev-dependencies - install/update all vendor libraries necessary for development"
	@echo ""
	@exit 0

test:
	@vendor/bin/phpunit

install-composer:
	@if [ ! -d ./bin ]; then mkdir bin; fi
	@if [ ! -f ./bin/composer ]; then \
		if ! which composer > /dev/null; then \
			curl -s http://getcomposer.org/installer \
				| php -n -d allow_url_fopen=1 -d date.timezone="Europe/Berlin" -- \
					--install-dir=./bin --filename=composer; \
		else \
			echo using existing $$(composer --version); \
			ln -s $$(which composer) ./bin/composer; \
		fi; \
	fi

install-dependencies:
	@make install-composer
	@php -n -d allow_url_fopen=1 -d date.timezone="Europe/Berlin" ./bin/composer -- install
	
.PHONY: test help

