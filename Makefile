MAKEFLAGS += --no-print-directory

map:
	./bin/jsm map sample/json sample/generated Json

test:
	@./vendor/bin/phpunit
stan:
	@./vendor/bin/phpstan analyse -l 7 -c phpstan.neon src
cs:
	@./vendor/bin/phpcs --standard=PSR2 src

check:
	-make stan test cs

.PHONY: test stan cs check
