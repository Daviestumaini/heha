FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev pkg-config \
    && docker-php-ext-install curl \
    && apt-get purge -y --auto-remove pkg-config \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . /app

EXPOSE 10000
CMD php -S 0.0.0.0:${PORT:-10000} -t /app
