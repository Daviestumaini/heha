FROM php:8.2-cli

RUN docker-php-ext-install curl

WORKDIR /app
COPY . /app

EXPOSE 10000
CMD php -S 0.0.0.0:${PORT:-10000} -t /app
