FROM php:7-cli
ADD otrsTool.phar /otrsTool.phar
WORKDIR /tmp/work
CMD [ "php", "/otrsTool.phar" ]