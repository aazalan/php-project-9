PORT ?= 8000
start:
	php -S 0.0.0.0:$(PORT) 	-t public

install:
	composer update
	cp -n .env.railway .env

install_local:
	composer update
	cp -n .env.example .env

dbrun:
	sudo /etc/init.d/mysql start

start_2:
	php -S localhost:8080 -t public public/index.php

lint:
	./vendor/bin/phpcs -h

connect:
	mysql -u root -p