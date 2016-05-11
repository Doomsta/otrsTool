build:
	composer install
	box build
build_docker: build
	docker build -t arandel/otrs_tool .
