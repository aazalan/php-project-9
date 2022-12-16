PORT ?= 8000
start:
	mysql -hcontainers-us-west-134.railway.app -uroot -pVGGNeJBPGpnKl7b4fc5Q --port 7880 --protocol=TCP railway
	php -S 0.0.0.0:$(PORT) 	-t public

install:
	composer update

dbrun:
	sudo /etc/init.d/mysql start

start2:
	php -S localhost:8080 -t public public/index.php

lint:
	./vendor/bin/phpcs -h

connect:
	mysql -u root -p