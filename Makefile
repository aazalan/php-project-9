PORT ?= 8000
start:
	php -S 0.0.0.0:$(PORT) 	-t public

start2:
	php -S localhost:8080 -t public public/index.php